<?php

class User_model extends \Myth\Models\CIDbModel {

    protected $table_name = 'users';

    protected $soft_deletes = true;

    protected $set_created = true;

    protected $set_modified = false;

    protected $protected_attributes = ['id', 'submit'];

    protected $validation_rules = [
        [
            'field' => 'first_name',
            'label' => 'First Name',
            'rules' => 'trim|alpha|max_length[255]|xss_clean'
        ],
        [
            'field' => 'last_name',
            'label' => 'Last Name',
            'rules' => 'trim|alpha|max_length[255]|xss_clean'
        ],
        [
            'field' => 'email',
            'label' => 'Email Address',
            'rules' => 'trim|valid_email|max_length[255]|xss_clean'
        ],
        [
            'field' => 'username',
            'label' => 'Username',
            'rules' => 'trim|alpha|max_length[255]|xss_clean'
        ],
        [
            'field' => 'password',
            'label' => 'Password',
            'rules' => 'trim|xss_clean|max_length[255]|isStrongPassword'
        ],
        [
            'field' => 'pass_confirm',
            'label' => 'Password (Again)',
            'rules' => 'trim|matches[password]'
        ],
    ];

    protected $insert_validate_rules = [
        'email'         => 'required|is_unique[users.email]',
        'username'      => 'required|is_unique[users.username]',
        'password'      => 'required',
        'pass_confirm'  => 'required'
    ];

    protected $before_insert = ['hashPassword'];
    protected $before_update = ['hashPassword'];
    protected $after_insert  = ['updateMeta'];
    protected $after_update  = ['updateMeta'];

    // The columns in the 'users_meta' table - for auto updating of profile information.
    protected $meta_fields = ['first_name', 'last_name'];

    protected $fields = ['id', 'role_id', 'email', 'username', 'password_hash', 'reset_hash', 'activate_hash', 'created_on', 'status', 'status_message', 'timezone', 'language', 'active', 'deleted', 'force_pass_reset'];

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
        $this->db->join('user_meta', 'users.id = user_meta.user_id', 'inner');

        return $this;
    }

    //--------------------------------------------------------------------



    /**
     * If exists, will take our password out of the data array, and
     * create a new hash for it, which is inserted back into the
     * data array to be saved to the database.
     *
     * @param $data
     */
    protected function hashPassword($data)
    {
        if (isset($data['password']))
        {
            $data['password_hash'] = \Myth\Auth\Password::hashPassword($data['password']);

            unset($data['password'], $data['pass_confirm']);
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
     * @param $data
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
                    'user_id'       => $data['id'],
                    'meta_key'      => $key,
                    'meta_value'    => $value
                ];

                if ($query->num_rows() == 0)
                {
                    $this->db->insert('user_meta', $obj);
                }
                else if ($query->num_rows() > 0) {
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


}