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
use Rf\Core\Base\Uri;

/**
 * Class Request
 *
 * @package Rf\Core\Http
 */
class Request {

    /** @var Uri Request uri */
    protected $uri;

    /** @var string HTTP Request method (GET|POST|PUT|PATCH|DELETE|OPTIONS|HEAD|TRACE|CONNECT) */
    protected $method;

    /** @var ParameterSet Headers data */
    protected $headers;

    /** @var string Content type */
    protected $contentType;

    /** @var ParameterSet Query data */
    protected $query;

    /** @var ParameterSet GET data */
    protected $getData;

    /** @var ParameterSet POST data */
    protected $postData;

    /** @var ParameterSet PUT data */
    protected $putData;

    /** @var ParameterSet DELETE data */
    protected $deleteData;

    /** @var ParameterSet Request data (all methods + files combined) */
    protected $requestData;

    /** @var ParameterSet Files data */
    protected $filesData;

    /** @var ParameterSet Server data */
    protected $serverData;

    /** @var ParameterSet */
    protected $sessionData;

    /** @var ParameterSet */
    protected $cookieData;

    /** @var bool */
    protected $isHttps;

    /** @var bool */
    protected $isAjax;

    /** @var bool */
    protected $isMobile;

    /** @var bool */
    protected $isApi;

    /**
     * Create a new Request object using the $_ variables and the current uri
     *
     * @throws \Exception
     */
    public function __construct() {

        // Map headers
        $this->headers = new ParameterSet(getallheaders());

        // Map $_SERVER params
        $this->serverData = new ParameterSet($_SERVER);

        // Map $_SESSION params ($_SESSION only filled if session is already started)
        $this->sessionData = new ParameterSet(isset($_SESSION) ? $_SESSION : []);

        // Map $_COOKIE params
        $this->cookieData = new ParameterSet(isset($_COOKIE) ? $_COOKIE : []);

        // Create a new Uri object with the current uri
        $this->uri = new Uri(Uri::INIT_WITH_CURRENT_URI);

        // Get request information from initialized params and uri
        $this->getRequestInformation();

        // Initialize a new query parameter set
        $this->query = new ParameterSet([]);

        // Map $_GET params
        $this->getData = new ParameterSet($_GET);

        // Map $_POST params
        $this->postData = new ParameterSet($_POST);

        // Map $_FILES params
        $this->filesData = new ParameterSet($_FILES);

        // Combine all data
        $this->requestData = new ParameterSet(array_merge($_GET, $_POST, $_FILES));

    }

    /**
     * Get request method
     *
     * @return string
     */
    public function getMethod() {

        if(!isset($this->method)) {
            $this->method = strtoupper($this->serverData->get('REQUEST_METHOD'));
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
            $contentTypeParts = explode(';', $this->serverData->get('CONTENT_TYPE'));
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

                if(!is_a($this->{$param}, ParameterSet::class)) {
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
     * Get current URI
     *
     * @return Uri
     */
    public function getUri() {

        return $this->uri;

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
     * Get query data
     *
     * @return ParameterSet
     */
    public function getQueryData() {

        return $this->query;

    }

    /**
     * Get GET data
     *
     * @return ParameterSet
     */
    public function getGetData() {

        return $this->getData;

    }

    /**
     * Get POST data
     *
     * @return ParameterSet
     */
    public function getPostData() {

        return $this->postData;

    }

    /**
     * Get DELETE data
     *
     * @return ParameterSet
     */
    public function getDeleteData() {

        return $this->deleteData;

    }

    /**
     * Get FILES data
     *
     * @return ParameterSet
     */
    public function getFilesData() {

        return $this->filesData;

    }

    /**
     * Get SERVER data
     *
     * @return ParameterSet
     */
    public function getServerData() {

        return $this->serverData;

    }

    /**
     * Get SESSION data
     *
     * @return ParameterSet
     */
    public function getSessionData() {

        return $this->sessionData;

    }

    /**
     * Get COOKIE data
     *
     * @return ParameterSet
     */
    public function getCookieData() {

        return $this->cookieData;

    }

    /**
     * Get request information
     *
     * @throws \Exception
     */
    private function getRequestInformation() {

        if(
            $this->get('server', 'HTTPS') !== false
            || $this->get('server', 'HTTPS') == 'on'
        ) {
            $this->isHttps = true;
        } else {
            $this->isHttps = false;
        }

        // @TODO: strpos check
        if($this->uri->host() == rf_sp()->getRouter()->getConfiguration()->get('url-mobile')) {
            $this->isMobile = true;
        } else {
            $this->isMobile = false;
        }

        if($this->uri->host() == rf_sp()->getRouter()->getConfiguration()->get('url-api')) {
            $this->isApi = true;
        } else {
            $this->isApi = false;
        }

        if(
            $this->serverData->get('HTTP_X_REQUESTED_WITH') !== false
            && $this->serverData->get('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest'
        ) {
            $this->isAjax = true;
        } else {
            $this->isAjax = false;
        }

        switch($this->getMethod()) {

            case 'GET':
                break;

            case 'POST':
            case 'PUT':
            case 'PATCH':

                if($this->getContentType() == 'application/json') {

                    // @TODO: Refactor
                    $phpInput = file_get_contents('php://input');
                    $postParams = json_decode($phpInput);

                    // @TODO: Add an option in app to prevent automatic processing
                    if($this->isFormData($postParams) === true) {
                        $_POST = $this->parseFormData(json_decode($phpInput, true));
                    } elseif($this->isApi()) {
                        $_POST = $postParams; // @TODO: Object or table ????
                    } else {

                        $parsedData = [];
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

                $this->deleteData = new ParameterSet($deleteParamsParsed);
                break;

            case 'HEAD':
                break;

            case 'OPTIONS':
                break;

            case 'TRACE':
                break;

            case 'CONNECT':
                break;

            default:
                throw new \Exception('Unsupported HTTP method');
                break;

        }

    }

    /**
     * Check if the data is form data: object->form === [...]
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

        return $this->uri->full();

    }

    /**
     * Return the visitor IP address
     *
     * @return string
     */
    public function getVisitorIp() {

        // Get real visitor IP behind CloudFlare network
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return $_SERVER['HTTP_CF_CONNECTING_IP'];
        }

        return $_SERVER['REMOTE_ADDR'];

    }

    /**
     * Check is the current request is a HTTPS request
     *
     * @return bool
     */
    public function isHttps() {

        // @TODO: Get this info from URI

        return $this->isHttps;

    }

    /**
     * Detect if the visitor is a known user agent (bot)
     *
     * @return bool
     */
    public function is_bot() {

        return (preg_match('([bB]ot|[sS]pider|[yY]ahoo)', $_SERVER['HTTP_USER_AGENT'])) ? true : false;

    }

    /**
     * Check is the current request is an API request
     *
     * @return bool
     */
    public function isApi() {

        return $this->isApi;

    }

    /**
     * Check is the current request is an AJAX request
     *
     * @return bool
     */
    public function isAjax() {

        return $this->isAjax;

    }

    /**
     * Check is the current request is a mobile request
     *
     * @return bool
     */
    public function isMobile() {

        return $this->isMobile;

    }

}

