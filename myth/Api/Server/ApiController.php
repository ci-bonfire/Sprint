<?php namespace Myth\Api\Server;
/**
 * Sprint
 *
 * A set of power tools to enhance the CodeIgniter framework and provide consistent workflow.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package     Sprint
 * @author      Lonnie Ezell
 * @copyright   Copyright 2014-2015, New Myth Media, LLC (http://newmythmedia.com)
 * @license     http://opensource.org/licenses/MIT  (MIT)
 * @link        http://sprintphp.com
 * @since       Version 1.0
 */

use Myth\Auth\AuthTrait;
use Myth\Controllers\BaseController;

/**
 * Class ApiController
 *
 * Provides a basic set of functionality to all API Controllers. Includes
 * helper methods for responding and failing.
 *
 * @package Myth\Api\Server
 */
class ApiController extends BaseController {

	use AuthTrait;

	protected $language_file = 'api';

	protected $ajax_notices = false;

	/**
	 * Holds all request parameters.
	 * @var array
	 */
	protected $vars = [];


	protected $request;

	protected $allowed_http_methods = [
		'get',
		'put',
		'post',
		'delete',
		'options',
		'patch',
		'head'
	];

	/**
	 * Turns off authorization checks.
	 * Only intended for temp use in
	 * development environments.
	 * @var bool
	 */
	protected $do_auth_check = true;

	/**
	 * The current page of results being requested.
	 * @var int
	 */
	protected $page = 0;

	/**
	 * The number of results to return per page
	 * of results, by default.
	 * @var int
	 */
	protected $per_page = 20;

	/**
	 * Based on the current page,
	 * used for LIMITing data requests
	 * from database.
	 *
	 * @var int
	 */
	protected $offset = 0;

	/**
	 * The time in microseconds that the request started.
	 *
	 * @var null
	 */
	protected $start_time = null;

	/**
	 * Specifies whether this request should be logged.
	 *
	 * @var bool
	 */
	protected $enable_logging;

	/**
	 * Whether rate limiting is enabled.
	 *
	 * @var bool
	 */
	protected $enable_rate_limits;

	/**
	 * The number of requests allowed per user/hour
	 *
	 * @var int
	 */
	protected $rate_limits = 0;

	/**
	 * Status strings/codes allowed when using
	 * the generic 'fail' method.
	 *
	 * @var array
	 */
	protected $codes = array(
		'invalid_request'           => 400,
		'unsupported_response_type' => 400,
		'invalid_scope'             => 400,
		'temporarily_unavailable'   => 400,
		'invalid_grant'             => 400,
		'invalid_credentials'       => 400,
		'invalid_refresh'           => 400,
		'no_data'                   => 400,
		'invalid_data'              => 400,
		'access_denied'             => 401,
		'unauthorized'              => 401,
		'invalid_client'            => 401,
		'forbidden'                 => 403,
		'resource_not_found'        => 404,
		'not_acceptable'            => 406,
		'resource_exists'           => 409,
		'resource_gone'             => 410,
		'too_many_requests'         => 429,
		'server_error'              => 500,
		'unsupported_grant_type'    => 501,
		'not_implemented'           => 501
	);



	//--------------------------------------------------------------------

	public function __construct()
	{
		$this->start_time = microtime(true);

		$this->request = new \stdClass();
		$this->request->ssl     = is_https();
		$this->request->method  = $this->detectMethod();
		$this->request->lang    = $this->detectLanguage();

		// Load our language, requested.
		if (!empty($this->request->lang))
		{
			$file = ! empty($this->language_file) ? $this->language_file : 'application';

			if (is_array($this->request->lang))
			{
				$this->load->language($file, $this->request->lang[0]);
			}
			else
			{
				$this->load->language($file, $this->request->lang);
			}

			unset($file);
		}

	    parent::__construct();

		$this->config->load('api');

		// Gather config defaults when a value isn't set for this controller
		if ( empty($this->enable_logging) ) $this->enable_logging = config_item('api.enable_logging');
		if ( empty($this->enable_rate_limits) ) $this->enable_rate_limits = config_item('api.enable_rate_limits');
		if ( empty($this->rate_limits) ) $this->rate_limits = config_item('api.rate_limits');

		// Should we restrict to SSL requests?
		if (config_item('require_ssl') === true && ! $this->request->ssl)
		{
			$this->failForbidden( lang('api.ssl_required') );
		}

		// Should we restrict to only allow AJAX requests?
		if (config_item('api.ajax_only') === true && ! $this->input->is_ajax_request() )
		{
			$this->failForbidden( lang('api.ajax_required') );
		}

		$this->detectPage();

		if ($this->do_auth_check)
		{
			if (! $this->restrict() )
			{
				$this->failUnauthorized( lang('api.unauthorized') );
			}
		}

		// Has the user hit rate limits for this hour?
		if ($this->enable_rate_limits && ! $this->isWithinLimits())
		{
			$this->failTooManyRequests( sprintf( lang('api.too_many_requests'), $this->rate_limits) );
		}

		// NEVER allow profiling via API.
		$this->output->enable_profiler(false);

		// Set logging default value
		$this->enable_logging = config_item('api.enable_logging');
	}

	//--------------------------------------------------------------------

	/**
	 * Responsible for enforcing SSL restrictions.
	 *
	 * @param $method
	 * @param array $arguments
	 *
	 * @return mixed
	 */
	public function _remap($method, $arguments = [])
	{
		// Now, run the right thing!
		if (method_exists($this, $method))
		{
			call_user_func_array([$this, $method], $arguments);

			if ($this->enable_logging === true)
			{
				$this->logTime();
			}
		}
		else
		{
			return $this->fail( lang('api.unknown_endpoint'), 'not_implemented');
		}
	}

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// Response Methods
	//--------------------------------------------------------------------

	/**
	 * Provides a single, simple method to return an API response, formatted
	 * as json, with the proper content type and status code.
	 *
	 * // todo Allow responses in other formats, like jsonp, html and csv
	 *
	 * @param     $data
	 * @param int $status_code
	 * @return mixed
	 */
	public function respond ($data = null, $status_code = null)
	{
		// If data is NULL and not code provide, error and bail
		if ($data === NULL && $status_code === NULL)
		{
			$status_code = 404;

			// create the output variable here in the case of $this->response(array());
			$output = NULL;
		}

		// If data is NULL but http code provided, keep the output empty
		else if ($data === NULL && is_numeric($status_code))
		{
			$output = NULL;
		}

		else
		{
			header('Content-Type: application/json');

			$output = json_encode($data);
		}

		set_status_header($status_code);

		header('Content-Length: ' . strlen($output));

		exit($output);
	}

	//--------------------------------------------------------------------

	/**
	 * Returns a failure code to the end user. Mainly so that we have a simple
	 * way to return a consistent response format.
	 *
	 * @param        $description
	 * @param        $status_code
	 * @param string $error_code
	 * @return mixed
	 */
	protected function fail ($description, $status_code, $error_code = 'invalid_request')
	{
		if (is_string($status_code))
		{
			$error_code  = $status_code;
			$status_code = in_array($status_code, $this->codes) ? $this->codes[$status_code] : 500;
		}

		$response = [
			'error'         => $error_code,
			'error_message' => $description
		];

		$this->respond($response, $status_code);
	}

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// Response Helpers
	//--------------------------------------------------------------------

	/**
	 * Used after successfully creating a new resource.
	 *
	 * @param $data
	 * @return mixed
	 */
	protected function respondCreated($data)
	{
		return $this->respond($data, 201, 'created');
	}

	//--------------------------------------------------------------------

	/**
	 * Used when a resource has been successfully deleted.
	 *
	 * @param $data
	 * @return mixed
	 */
	protected function respondDeleted($data)
	{
		return $this->respond($data, 204, 'deleted');
	}

	//--------------------------------------------------------------------

	/**
	 * Used
	 *
	 * @param $description
	 *
	 * @return mixed
	 */
	protected function failUnauthorized($description)
	{
		return $this->fail($description, 'unauthorized');
	}

	//--------------------------------------------------------------------

	/**
	 * Used when access to this resource is not allowed. Authorization
	 * will not help.
	 *
	 * @param $description
	 *
	 * @return mixed
	 */
	public function failForbidden($description)
	{
		return $this->fail($description, 'forbidden');
	}

	//--------------------------------------------------------------------

	/**
	 * Used when the resource the request is for cannot be found.
	 *
	 * @param $description
	 *
	 * @return mixed
	 */
	protected function failNotFound($description)
	{
		return $this->fail($description, 'resource_not_found');
	}

	//--------------------------------------------------------------------

	/**
	 * Used for when invalid data is presented to the API.
	 *
	 * @param $description
	 * @return mixed
	 */
	protected function failBadRequest($description)
	{
		return $this->fail($description, 'invalid_request');
	}

	//--------------------------------------------------------------------

	/**
	 * Used when the data does not validate. Separate for better
	 * readability and in case we ever change the response code
	 * in the future.
	 *
	 * @param $description
	 *
	 * @return mixed
	 */
	protected function failValidationError($description)
	{
		return $this->fail($description, 'invalid_request');
	}

	//--------------------------------------------------------------------

	/**
	 * Used when trying to create a new resource and it already exists.
	 *
	 * @param $description
	 *
	 * @return mixed
	 */
	protected function failResourceExists($description)
	{
		return $this->fail($description, 'resource_exists');
	}

	//--------------------------------------------------------------------

	/**
	 * Used when the resource has intentionally been removed already and will not
	 * be available again. Like when its already been deleted.
	 *
	 * @param $description
	 * @return mixed
	 */
	protected function failResourceGone($description)
	{
		return $this->fail($description, 'resource_gone');
	}

	//--------------------------------------------------------------------

	/**
	 * Used when the user has made too many requests against the  within
	 * the last hour.
	 *
	 * @param $description
	 *
	 * @return mixed
	 */
	protected function failTooManyRequests($description)
	{
		return $this->fail($description, 'too_many_requests');
	}

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// Utility Methods
	//--------------------------------------------------------------------

	/**
	 * @param $name
	 *
	 * @return bool
	 */
	public function grabVar($name)
	{
	    return array_key_exists($name, $this->vars) ? $this->vars[$name] : false;
	}

	//--------------------------------------------------------------------

	/**
	 * Creates the URL for the next set of results based on the
	 * 'page' value set in the calling URL.
	 *
	 * If $clean_get is TRUE will only include the ?page value on
	 * the URL, otherwise will include all $_GET values that were
	 * sent to the URL.
	 *
	 * Returns NULL if this request has had paging turned off,
	 * via ?page=0.
	 *
	 * @param $path
	 * @param $clean_get
	 *
	 * @return string
	 */
	public function nextURL($path, $clean_get = false)
	{
		// If paging is turned off, get out of here
		if ($this->per_page == 0)
		{
			return null;
		}

		$params = [];

		$params['page'] = ($this->page > 1 ? $this->page + 1 : 2);

		if (! $clean_get)
		{
			if ( ! isset( $_GET ) || ! is_array( $_GET ) )
			{
				$_GET = [ ];
			}

			foreach ( $_GET as $key => $value )
			{
				$params[ $key ] = $value;
			}

			// Ensure we get a correct per_page value
			if (! array_key_exists('per_page', $params))
			{
				$params['per_page'] = $this->per_page;
			}
		}

		return site_url($path) . '?' . http_build_query($params);
	}

	//--------------------------------------------------------------------

	/**
	 * Creates the URL for the prev set of results based on the
	 * 'page' value set in the calling URL.
	 *
	 * If $clean_get is TRUE will only include the ?page value on
	 * the URL, otherwise will include all $_GET values that were
	 * sent to the URL.
	 *
	 * Returns NULL if this request has had paging turned off,
	 * via ?page=0.
	 *
	 * @param $path
	 * @param bool $clean_get
	 *
	 * @return string
	 */
	public function prevURL ($path, $clean_get = false)
	{
		// If paging is turned off, get out of here
		if ($this->per_page == 0)
		{
			return null;
		}

		$params = [];

		$params['page'] = ($this->page > 1 ? $this->page - 1 : 1);

		if (! $clean_get)
		{
			if ( ! isset( $_GET ) || ! is_array( $_GET ) )
			{
				$_GET = [ ];
			}

			foreach ( $_GET as $key => $value )
			{
				$params[ $key ] = $value;
			}

			// Ensure we get a correct per_page value
			if (! array_key_exists('per_page', $params))
			{
				$params['per_page'] = $this->per_page;
			}
		}

		return site_url($path) . '?' . http_build_query($params);
	}

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// Internal Methods
	//--------------------------------------------------------------------

	/**
	 * Determines the current page and offset based upon a ?page $_GET var.
	 *
	 * The offset value is based on the current $this->per_page value.
	 *
	 * A request can set ?page=0 to turn off paging altogether.
	 */
	protected function detectPage( )
	{
		// Is a per-page limit being set?
		if ($count = $this->grabVar('per_page'))
		{
			$this->per_page = (int)$count;
			unset($count);
		}

		$page = (int)$this->input->get('page');

		if (! $page || $page == 1)
		{
			$offset = 0;
		}
		else
		{
			$offset = (($page - 1) * $this->per_page) + 1;
		}

		$this->page   = $page;
		$this->offset = $offset;

		// If they've specifically passed in page=0, then we need
		// to ignore paging...
		if ((int)$this->input->get('page') === 0 && $this->input->get('page') !== false)
		{
			$this->per_page = 0;
		}
	}

	//--------------------------------------------------------------------

	/**
	 * Detects the request method and populates the $vars array based on
	 * the method found.
	 *
	 * NOTE that any $_GET vars will have to be accessed by the standard
	 * methods when the method isn't a GET request.
	 *
	 * @return string
	 */
	protected function detectMethod()
	{
		$method = strtolower($this->input->server('REQUEST_METHOD'));

		// If it's not an allowed method, let's default to a GET
		if (! in_array($method, $this->allowed_http_methods))
		{
			$method = 'get';
		}

		// Populate our $vars based on the input type.
		switch ($method)
		{
			case 'get':
				$this->vars = $_GET;
				break;
			case 'post':
				$this->vars = $_POST;
				break;
			default:
				$this->vars = $this->getJSON('array');
				break;
		}

		return $method;
	}

	//--------------------------------------------------------------------

	/**
	 * Detects one or more languages that should the request should be
	 * returned as. If more than 1 exists, just load the first language
	 * file.
	 *
	 * @return array|mixed|null
	 */
	protected function detectLanguage()
	{
		if ( ! $lang = $this->input->server('HTTP_ACCEPT_LANGUAGE'))
		{
			return null;
		}

		// They might have sent a few, make it an array
		if (strpos($lang, ',') !== false)
		{
			$langs = explode(',', $lang);

			$return_langs = array();

			foreach ($langs as $lang)
			{
				// Remove weight and strip space
				list($lang) = explode(';', $lang);
				$return_langs[] = trim($lang);
			}

			return $return_langs;
		}

		// Nope, just return the string
		return $lang;
	}

	//--------------------------------------------------------------------

	/**
	 * Takes care of logging the request information to the database.
	 */
	public function logTime()
	{
	    $model = new LogModel();

		$data = [
			'duration' => microtime(true) - $this->start_time,
			'user_id'  => $this->auth->id(),
			'request'  => $this->uri->uri_string() ."?". $_SERVER['QUERY_STRING'],
			'method'   => $this->request->method
		];

		$model->insert($data);
	}

	//--------------------------------------------------------------------

	/**
	 * Checks the user's number of requests within the current hour.
	 * Returns true if they are within their limits and can make additional
	 * requests. Returns false if they have exceeded the number of requests
	 * for this hour.
	 *
	 * @return bool
	 */
	private function isWithinLimits()
	{
		$model = new LogModel();

		if ($model->requestsThisHourForUser( $this->id() ) > $this->rate_limits)
		{
			return false;
		}

		return true;
	}

	//--------------------------------------------------------------------

}
