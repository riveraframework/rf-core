<?php

namespace Rf\Core\Http;

/**
 * Interface ResponseInterface
 * @package Rf\Core\Http
 */
interface ResponseInterface {

    public function __construct($httpCode = 200);
    public function addHeader($option, $value);
    public function setBody($body);
    public function send();

}