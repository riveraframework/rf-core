<?php

namespace Rf\Core\Application;

/**
 * Interface ApplicationInterface
 *
 * @package Rf\Core\Application
 */
interface ApplicationInterface {

    public function init();
    public static function getInstance();

}