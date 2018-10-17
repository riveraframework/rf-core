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

/**
 * Class Response
 *
 * @package Rf\Core\Http
 */
class Response {

    /** @var int Response HTTP code */
    public $httpCode;

    /** @var string Response content type */
    public $contentType;

    /** @var array Response headers */
    public $headers = [];

    /** @var string Response body */
    public $body;

    /** @var string Response default HTTP version */
    public static $defaultHttpVersion = '1.1';

    /** @var array Available HTTP codes */
    public static $availableHttpCodes = [

        // Information Codes
        100 => 'Continue',
        101 => 'Switching Protocols',

        // Success Codes
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',

        // Redirection Codes
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Moved Temporarily',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',

        // Client Errors
        400 => 'Bad Request',
        401 => 'Unauthorized',
        // 402 => 'Payment Required', // Not available with HTTP
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested range unsatifiable',
        417 => 'Expectation failed',

        // Server Errors
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',

    ];

    /**
     * Create a new HTTP Response
     *
     * @param int $httpCode Response HTTP version
     */
    public function __construct($httpCode = 200) {

        $this->httpCode = $httpCode;

    }

    /**
     * Change the code of the response
     *
     * @param int $httpCode
     */
    public function changeCode($httpCode) {

        $this->httpCode = $httpCode;

    }

    /**
     * Add a header to the response
     *
     * @param string $option Header name
     * @param string $value Header value
     */
    public function addHeader($option, $value) {

        $this->headers[$option] = $value;

    }

    /**
     * Set the response content type
     *
     * @param string $contentType Response content type
     */
    public function setContentType($contentType) {

        $this->addHeader('Content-Type', $contentType);

    }

    /**
     * Set the response body
     *
     * @param string $body Response body
     */
    public function setBody($body) {

        $this->body = $body;

    }

    /**
     * Send the response
     */
    public function send() {

        if(!empty($this->httpCode) && in_array($this->httpCode, array_keys(self::$availableHttpCodes))) {
            header('HTTP/' . self::$defaultHttpVersion . ' ' . $this->httpCode . ' ' . self::$availableHttpCodes[$this->httpCode]);
        } else {
            return;
        }

        foreach ($this->headers as $option => $value) {
            header($option . ': ' . $value);
        }

        if(!empty($this->body)) {
            echo $this->body;
        }

        exit;

    }

}