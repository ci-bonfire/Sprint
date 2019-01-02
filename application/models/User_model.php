<?php

use Myth\Events\Events;

class User_model extends \Myth\Models\CIDbModel {

    protected $table_name = 'users';

    protected $soft_deletes = true;

    protected $set_created = true;

    protected $set_modified = false;

    protected $protected_attributes = ['id', 'submit'];

    protected $validation_rules = [
        [
            'field' => 'first_name',
            'label' => 'lang:auth.first_name',
            'rules' => 'trim|alpha|max_length[255]'
        ],
        [
            'field' => 'last_name',
            'label' => 'lang:auth.last_name',
            'rules' => 'trim|alpha|max_length[255]'
        ],
        [
            'field' => 'email',
            'label' => 'lang:auth.email',
            'rules' => 'trim|valid_email|max_length[255]'
        ],
        [
            'field' => 'username',
            'label' => 'lang:auth.username',
            'rules' => 'trim|alpha_numeric|max_length[255]'
        ],
        [
            'field' => 'password',
            'label' => 'lang:auth.password',
            'rules' => 'trim|max_length[255]|isStrongPassword'
        ],
        [
            'field' => 'pass_confirm',
            'label' => 'lang:auth.pass_confirm',
            'rules' => 'trim|matches[password]'
        ],
    ];

    protected $insert_validate_rules = [
        'email'        => 'required|is_unique[users.email]',
        'username'     => 'required|is_unique[users.username]',
        'password'     => 'required',
        'pass_confirm' => 'required'
    ];

    protected $before_insert = ['hashPassword'];
    protected $before_update = ['hashPassword'];
    protected $after_insert  = ['updateMeta'];
    protected $after_update  = ['updateMeta'];

    // The columns in the 'users_meta' table - for auto updating of profile information.
    protected $meta_fields = ['first_name', 'last_name'];

    protected $fields = ['id', 'email', 'username', 'password_hash', 'reset_hash', 'activate_hash', 'created_on', 'status', 'status_message', 'active', 'deleted', 'force_pass_reset'];

    //--------------------------------------------------------------------

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('auth/password');
    }

    //--------------------------------------------------------------------

    /**
     * Works with any find queries to return user_meta information.
     *
     * @return $this
     */
    public function withMeta()
    {
        $this->after_find[] = 'grabMeta';

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * If exists, will take our password out of the data array, and
     * create a new hash for it, which is inserted back into the
     * data array to be saved to the database.
     *
     * @param array $data
     *
     * @return array
     */
    protected function hashPassword($data)
    {
        if (isset($data['fields']))
        {
            $data = $data['fields'];
        }

        if (isset($data['password']))
        {
            $data['password_hash'] = \Myth\Auth\Password::hashPassword($data['password']);

            unset($data['password'], $data['pass_confirm']);
        }

        return $data;
    }

    //--------------------------------------------------------------------

    /**
     * A callback designed to work with Digest Authentication to create
     * and store the $A1 value since we'll never have access to the
     * password except during inserts or updates.
     *
     * This assumes that this is working as part of an API and that
     * the api config file is already loaded into memory.
     *
     * @param $data
     *
     * @return $data
     */
    public function createDigestKey($data)
    {
        $field = config_item('api.auth_field');
        $value = null;

        // If it's an update, we probably won't have the username/email
        // so grab it so that we can use it.
        if (! empty($data[ $this->primary_key ]))
        {
            if (! isset($data[$field]) )
            {
                $value = $this->get_field( $data['id'], $field );
            }
        }
        // However, if it's an insert, then we should have it, If we don't, leave.
        else
        {
            if (empty($data[$field]))
            {
                return $data;
            }

            $value = $data[$field];
        }

        // Still here? then create the hash based on the current realm.
        if (! empty($data['password']) )
        {
            $key = md5($value .':'. config_item('api.realm') .':'. $data['password']);
            $data['api_key'] = $key;
        }

        return $data;
    }

    //--------------------------------------------------------------------


    /**
     * A callback method intended to hook into the after_insert and after_udpate
     * methods.
     *
     * NOTE: Will only work for insert and update methods.
     *
     * @param array $data
     * @return mixed
     */
    public function updateMeta($data)
    {
        // If no 'id' is in the $data array, then
        // we don't have successful insert, get out of here
        if (empty($data['id']) || ($data['method'] != 'insert' && $data['method'] != 'update'))
        {
            return $data;
        }

        // Collect any meta fields
        foreach ($data['fields'] as $key => $value)
        {
            if (in_array($key, $this->meta_fields))
            {
                $this->db->where('user_id', $data['id']);
                $this->db->where('meta_key', $key);
                $query = $this->db->get('user_meta');

                $obj = [
                    'user_id'    => $data['id'],
                    'meta_key'   => $key,
                    'meta_value' => $value
                ];

                if ($query->num_rows() == 0)
                {
                    $this->db->insert('user_meta', $obj);
                }
                else if ($query->num_rows() > 0)
                {
                    $this->db->where('user_id', $data['id'])
                             ->where('meta_key', $key)
                             ->set('meta_value', $value)
                             ->update('user_meta', $obj);
                }
            }
        }

        return $data;
    }

    //--------------------------------------------------------------------

    /**
     * Adds a single piece of meta information to a user.
     *
     * @param $user_id
     * @param $key
     * @param null $value
     *
     * @return object
     */
    public function saveMetaToUser($user_id, $key, $value=null)
    {
        if (! Events::trigger('beforeAddMetaToUser', [$user_id, $key]))
        {
            return false;
        }

        $user_id = (int)$user_id;

        // Does this key already exist?
        $test = $this->db->where([ 'user_id' => $user_id, 'meta_key' => $key ])->get('user_meta');

        // Doesn't exist, so insert it.
        if (! $test->num_rows())
        {
            $data = [
                'user_id'       => $user_id,
                'meta_key'      => $key,
                'meta_value'    => $value
            ];

            return $this->db->insert('user_meta', $data);
        }

        // Otherwise, we need to update the existing.
        return $this->db->where('user_id', $user_id)
                        ->where('meta_key', $key)
                        ->set('meta_value', $value)
                        ->update('user_meta');
    }
    
    //--------------------------------------------------------------------

    /**
     * Gets the value of a single Meta item from a user.
     *
     * @param $user_id
     * @param $key
     *
     * @return mixed
     */
    public function getMetaItem($user_id, $key)
    {
        $query = $this->db->where('user_id', (int)$user_id)
                          ->where('meta_key', $key)
                          ->select('meta_value')
                          ->get('user_meta');

        if (! $query->num_rows())
        {
            return null;
        }

        return $query->row()->meta_value;
    }

    //--------------------------------------------------------------------

    /**
     * Deletes one or more meta values from a user.
     *
     * @param $user_id
     * @param $key
     *
     * @return bool
     */
    public function removeMetaFromUser($user_id, $key)
    {
        if (! Events::trigger('beforeRemoveMetaFromUser', [$user_id, $key]))
        {
            return false;
        }

	    if (is_array($key))
	    {
		    $this->db->where_in('meta_key', $key);
	    }
	    else
	    {
		    $this->db->where('meta_key', $key);
	    }

        $this->db->where('user_id', (int)$user_id)
                 ->delete('user_meta');
    }

    //--------------------------------------------------------------------

    public function getMetaForUser($user_id)
    {
        $query = $this->db->where('user_id', (int)$user_id)
                          ->select('meta_key, meta_value')
                          ->get('user_meta');

        $rows = $query->result();

        $meta = [];

        if (count($rows))
        {
            array_walk( $rows, function ( $row ) use ( &$meta )
            {
                $meta[ $row->meta_key ] = $row->meta_value;
            } );
        }

        return $meta;
    }

    //--------------------------------------------------------------------

    protected function grabMeta($data)
    {
        if (strpos($data['method'], 'find') === false)
        {
            return $data;
        }

        $meta = $this->getMetaForUser($data['fields']->id);

        if (is_object($data['fields']))
        {
            $data['fields']->meta = (object)$meta;
        }
        else
        {
            $data['fields']['meta']= $meta;
        }

        return $data;
    }

    //--------------------------------------------------------------------


}
