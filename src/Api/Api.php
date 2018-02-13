<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Api;

use Rf\Core\Application\Application;
use Rf\Core\Uri\CurrentUri;
use Rf\Core\Http\Response as HttpResponse;

/**
 * Class Api
 * Contain methods to handle API requests (CURL) and generating responses
 *
 * @package Rf\Core\Api
 *
 * @TODO: Replace $format by constants
 * @TODO: Configure API in app instead of Rf
 */
abstract class Api {

	const FORMAT_JSON =  'json';

    /** @var array Contain the mapping between status keywords and states */
    public static $keywords = array(
        'success' => 'success',
        'fail' => 'fail',
        'error' => 'error'
    );

    /**
     * This function get params for internal API requests and init a CURL connexion
     * to get the data cross (sub)domains.
     *
     * @return void
     */
    public static function handleRequest() {

        if(Application::getInstance()->getRequest()->get('request_type') !== 'api_follow') {

            // If the request type is different from "api_follow" an error is returned (json format)
            $httpResponse = new HttpResponse(200);
            $httpResponse->setBody(self::buildErrorResponse(null, 'json', 'Request type not supported'));
            $httpResponse->send();

        } else {
            
            // Build API url
            $url = CurrentUri::getProtocol() . '://' . rf_config('api.domain');
            $urlApiPart = strstr('/' . CurrentUri::getQuery(), '/api');
            $urlApiTarget = substr($urlApiPart, 4);
            $url .= $urlApiTarget;

            // Add GET parameters to query if some are present
            $get = $_GET;
            if(!empty($get)) {
                $url .= '?'. http_build_query($get);
            }
            
            // Define headers
            $headers = array (
                "Content-Type: application/json"
            );
            
            // Define variables to send
            $vars = json_encode(array_merge($_POST, $_FILES));
            
            // Prepare session
            $strCookie = 'PHPSESSID=' . $_COOKIE['PHPSESSID'] . '; path=/';
            session_write_close();            

            // Build the CURL request
            $handle = curl_init(); 
            curl_setopt($handle, CURLOPT_URL, $url);
            curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($handle, CURLOPT_COOKIE, $strCookie);
            
            if(Application::getInstance()->getRequest()->get('request_method') === 'DELETE') {
                curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'DELETE');
            } elseif(Application::getInstance()->getRequest()->get('request_method') === 'PUT') {
                curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($handle, CURLOPT_POSTFIELDS, $vars);
            } elseif(Application::getInstance()->getRequest()->get('request_method') === 'POST') {
                curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($handle, CURLOPT_POSTFIELDS, $vars);
            }

            // Execute CURL request and get results
            $result = curl_exec($handle);

            // Return the request result with a 200 HTTP code
            $httpResponse = new HttpResponse(200);
            if($result !== false) {
                $httpResponse->setBody($result);
            } else {
                $httpResponse->setBody(self::buildErrorResponse(null, 'json', curl_error($handle)));
            }
            curl_close($handle);
            $httpResponse->send();
        }

    }

    /**
     * Build a response with the parameters and return it in the right format
     *
     * @param string $type Type of the response (corresponding to success|fail|error)
     * @param array $data Additional data to return within the response
     * @param string $format Return format
     * @param string $message Message
     * @param string $errCode Error code
     *
     * @return array|string
     */
    public static function buildResponse($type, $data, $format = 'array', $message = '', $errCode = '') {

        $response = array();

        if(in_array($type, self::$keywords)) {
            $response['status'] = $type;
            $response['data'] = $data;
            $response['message'] = $message;
            $response['code'] = $errCode;
        } else {
            $response['status'] = self::$keywords['error'];
            $response['message'] = 'Invalid response status';
        }

        if($format == 'json') {

            $json = json_encode($response);

            if(json_last_error() === JSON_ERROR_UTF8) {
                $response['message'] = utf8_encode($response['message']);
                $json = json_encode($response);
            }
            return $json;

        } else {
            return $response;
        }

    }

    /**
     * Build a success response
     *
     * @param array $data Additional data to return within the response
     * @param string $format Return format
     * @param string $message Message
     *
     * @return array|string
     */
    public static function buildSuccessResponse($data, $format = 'array', $message = '') {

        return self::buildResponse('success', $data, $format, $message);

    }

    /**
     * Build a fail response
     *
     * @param array $data Additional data to return within the response
     * @param string $format Return format
     * @param string $message Message
     *
     * @return array|string
     */
    public static function buildFailResponse($data, $format = 'array', $message = '') {

        return self::buildResponse('fail', $data, $format, $message);

    }

    /**
     * Build an error response
     *
     * @param array $data Additional data to return within the response
     * @param string $format Return format
     * @param string $message Message
     * @param string $errCode Error code
     *
     * @return array|string
     */
    public static function buildErrorResponse($data, $format = 'array', $message = '', $errCode = '') {

        return self::buildResponse('error', $data, $format, $message, $errCode);

    }

}