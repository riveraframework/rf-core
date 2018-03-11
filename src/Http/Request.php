<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Http;

use Rf\Core\Base\ParameterSet;
use Rf\Core\Uri\CurrentUri;
use Rf\Core\Uri\Uri;

/**
 * Class Request
 *
 * @package Rf\Core\Http
 */
class Request {
    
    /** @var \Rf\Core\Uri\Uri Request uri */
    public $uri;

    /** @var string Request method (GET|POST|PUT|DELETE) */
    public $method;

    /** @var ParameterSet Headers data */
    public $headers;

    /** @var string Content type */
    public $contentType;

    /** @var ParameterSet Query data */
    public $query;

    /** @var ParameterSet GET data */
    public $get;

    /** @var ParameterSet POST data */
    public $post;

    /** @var ParameterSet PUT data */
    public $put;

    /** @var ParameterSet DELETE data */
    public $delete;

    /** @var ParameterSet Files data */
    public $files;

    /** @var ParameterSet Server data */
    public $server;

    /** @var ParameterSet */
    public $session;

    /** @var ParameterSet */
    public $cookie;

	/** @var bool */
	protected $isHttps;

	/** @var bool */
	protected $isAjax;

	/** @var bool */
	protected $isMobile;

	/** @var bool */
	protected $isApi;

	/** @var bool */
	protected $isApiFollow;

    /**
     * Create a new Request object using the $_ variables and the current uri
     */
    public function __construct() {

        // Map headers
        $this->headers = new ParameterSet(getallheaders());

        // Map $_SERVER params
        $this->server = new ParameterSet($_SERVER);

        // Map $_SESSION params
        $this->session = new ParameterSet($_SESSION);

        // Map $_COOKIE params
        $this->cookie = new ParameterSet($_COOKIE);

        // Create a new Uri object with the current uri
        $this->uri = new Uri(Uri::INIT_WITH_CURRENT_URI);

        // Get request information from initialized params and uri
        $this->getRequestInformation();

        // Initialize a new query parameter set
        $this->query = new ParameterSet([]);

        // Map $_GET params
        $this->get = new ParameterSet($_GET);

        // Map $_POST params
        $this->post = new ParameterSet($_POST);

        // Map $_FILES params
        $this->files = new ParameterSet($_FILES);

    }

    /**
     * Get request method
     *
     * @return string
     */
    public function getMethod() {

        if(!isset($this->method)) {
            $this->method = strtoupper($this->server->get('REQUEST_METHOD'));
        }

        return $this->method;

    }

    /**
     * Get request content type
     *
     * @return string
     */
    public function getContentType() {

        if(!isset($this->contentType)) {
            $contentTypeParts = explode(';', $this->server->get('CONTENT_TYPE'));
            $this->contentType = $contentTypeParts[0];
        }

        return $this->contentType;

    }

    /**
     * Get a parameter
     *
     * @param string $param
     * @param null|string $subParam
     *
     * @return mixed|false
     */
    public function get($param, $subParam = null) {

        if($param === 'request') {

            $get = $this->get('get', $subParam);

            if($get === false) {
                return $this->get('post', $subParam);
            } else {
                return $get;
            }

        } elseif(property_exists($this, $param) && !empty($this->{$param})) {

            if(!empty($subParam)) {

                if(!is_a($this->{$param}, 'ParameterSet')) {
                    return false;
                } else {
                    return $this->{$param}->get($subParam);
                }

            } else {

                return $this->{$param};

            }
        } else {

            return false;

        }
    }

    /**
     * Set a parameter
     *
     * @param string $param
     * @param mixed $value
     * @param null|string $subParam
     */
    public function set($param, $value, $subParam = null) {

        if(empty($subParam)) {
            $this->{$param} = $value;
        } else {
            $this->{$param}->set($subParam, $value);
        }

    }

	/**
	 * Get headers
	 *
	 * @return ParameterSet
	 */
    public function getHeaders() {

    	return $this->headers;

    }

	/**
	 * Get GET data
	 *
	 * @return ParameterSet
	 */
    public function getGetData() {

    	return $this->get;

    }

	/**
	 * Get POST data
	 *
	 * @return ParameterSet
	 */
    public function getPostData() {

    	return $this->post;

    }

    /**
     * Get FILES data
     *
     * @return ParameterSet
     */
    public function getFilesData() {

        return $this->files;

    }

    /**
     * Get SERVER data
     *
     * @return ParameterSet
     */
    public function getServerData() {

        return $this->server;

    }

    /**
     * Get SESSION data
     *
     * @return ParameterSet
     */
    public function getSessionData() {

        return $this->session;

    }

    /**
     * Get COOKIE data
     *
     * @return ParameterSet
     */
    public function getCookieData() {

        return $this->cookie;

    }

    /**
     * Get request information
     */
    private function getRequestInformation() {

	    if($this->get('server', 'HTTPS') !== false || $this->get('server', 'HTTPS') == 'on') {
		    $this->isHttps = true;
	    } else {
		    $this->isHttps = false;
	    }

    	if(CurrentUri::getHost() == rf_config('app.domain-mobile')) {
    		$this->isMobile = true;
	    } else {
	    	$this->isMobile = false;
	    }

    	if(CurrentUri::getHost() == rf_config('app.domain-api')) {
    		$this->isApi = true;
            header('Allow: OPTIONS, GET, POST, PUT, DELETE');
            header('Access-Control-Allow-Origin: *'); // http://' . rf_config('api.domain')
            header('Access-Control-Allow-Methods: OPTIONS, GET, POST, PUT, DELETE');
	    } else {
	    	$this->isApi = false;
	    }

        if($this->server->get('HTTP_X_REQUESTED_WITH') !== false && $this->server->get('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest') {
            $this->isAjax = true;
        } else {
	        $this->isAjax = false;
        }

        $this->isApiFollow = false;

        switch($this->getMethod()) {

            case 'GET':
                break;

            case 'POST':
            case 'PUT':

                if($this->getContentType() == 'application/json') {

                    $phpInput = file_get_contents('php://input');
                    $postParams = json_decode($phpInput);

                    if($this->isFormData($postParams) === true) {
                        $_POST = $this->parseFormData(json_decode($phpInput, true));
                    } elseif($this->isApi()) {
                        $_POST = $postParams; // Objet ou tableau ????
                    } else {

                        $parsedData = array();
                        if(!empty($postParams)) {
                            foreach($postParams as $prop => $field) {
                                if(isset($prop) && isset($field)) {
                                    $parsedData[$prop] = $field;
                                }
                            }
                        }
                        $_POST = $parsedData;

                    }

                }
                break;

            case 'DELETE':

                $deleteParams = file_get_contents('php://input');

                if($this->getContentType() == 'application/json') {
                    $deleteParamsParsed = json_decode($deleteParams);
                } else {
                    parse_str($deleteParams, $deleteParamsParsed);
                }

                $this->delete = new ParameterSet($deleteParamsParsed);

                break;

            default:
                $this->method = 'GET';
                break;

        }

    }

    /**
     * Check if the data is form data: object->form === array(...)
     *
     * @param object $data
     *
     * @return bool
     */
    private function isFormData($data) {

        return (isset($data->form) && is_array($data->form)) ? true : false;

    }

    /**
     * Parse form data
     *
     * @param array $data
     *
     * @return mixed
     */
    private function parseFormData($data) {

        foreach($data['form'] as $field) {

            if(isset($field['name']) && isset($field['value'])) {
                $data[$field['name']] = $field['value'];
            }

        }

        return $data;

    }

    /**
     * Get the request full url
     *
     * @return string
     */
    public function getFullUrl() {

        $s = $this->isHttps() ? 's' : '';
        $protocol = substr(strtolower($_SERVER['SERVER_PROTOCOL']), 0, strpos(strtolower($_SERVER['SERVER_PROTOCOL']), '/')) . $s;
        $port = ($_SERVER['SERVER_PORT'] == '80') ? '' : (':'.$_SERVER['SERVER_PORT']);

        return $protocol . '://' . $_SERVER['SERVER_NAME'] . $port . $_SERVER['REQUEST_URI'];

    }

    /**
     * Check is the current request is a HTTPS request
     *
     * @return bool
     */
    public function isHttps() {

    	return $this->isHttps;

    }

    /**
     * Check is the current request is an API request
     *
     * @return bool
     */
    final public function isApi() {

        return $this->isApi;

    }

    /**
     * Check is the current request is an API request
     *
     * @return bool
     */
    final public function isApiFollow() {

        return $this->isApiFollow;

    }

    /**
     * Check is the current request is an AJAX request
     *
     * @return bool
     */
    final public function isAjax() {

        return $this->isAjax;

    }

    /**
     * Check is the current request is a mobile request
     *
     * @return bool
     */
    final public function isMobile() {

        return $this->isMobile;

    }

}

