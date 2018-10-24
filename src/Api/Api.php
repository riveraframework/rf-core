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

/**
 * Class Api
 * Contain methods to handle API requests (CURL) and generating responses
 *
 * @package Rf\Core\Api
 *
 * @TODO: Replace static class by normal class and use formats class to build the response
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