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
 *
 * @package Rf\Core\Api
 */
abstract class ApiResponse {

    /** @var string  */
    const TYPE_SUCCESS = 'success';

    /** @var string  */
    const TYPE_FAIL = 'fail';

    /** @var string  */
    const TYPE_ERROR = 'error';

    /**
     * Build a response with the parameters and return it in the right format
     *
     * @param string $type Type of the response (corresponding to success|fail|error)
     * @param array $data Additional data to return within the response
     * @param string $message Message
     * @param string $errCode Error code
     *
     * @return array|string
     */
    public static function build($type, array $data = [], $message = '', $errCode = '') {

        $response = [];

        if(in_array($type, [
            self::TYPE_SUCCESS,
            self::TYPE_FAIL,
            self::TYPE_ERROR,
        ])) {

            $response['status'] = $type;
            $response['data'] = $data;
            $response['message'] = $message;
            $response['code'] = $errCode;

        } else {

            $response['status'] = self::TYPE_ERROR;
            $response['message'] = 'Invalid response status';
            
        }

        return $response;

    }

    /**
     * Build a success response
     *
     * @param array $data Additional data to return within the response
     * @param string $message Message
     *
     * @return array|string
     */
    public static function success(array $data = [], $message = '') {

        return self::build(self::TYPE_SUCCESS, $data, $message);

    }

    /**
     * Build a fail response
     *
     * @param array $data Additional data to return within the response
     * @param string $message Message
     *
     * @return array|string
     */
    public static function fail(array $data = [], $message = '') {

        return self::build(self::TYPE_FAIL, $data, $message);

    }

    /**
     * Build an error response
     *
     * @param array $data Additional data to return within the response
     * @param string $message Message
     * @param string $errCode Error code
     *
     * @return array|string
     */
    public static function error(array $data = [], $message = '', $errCode = '') {

        return self::build(self::TYPE_ERROR, $data, $message, $errCode);

    }

}