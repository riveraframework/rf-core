<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Uri;

/**
 * Class CurrentUri
 *
 * @since 1.0
 *
 * @package Rf\Core\Uri
 *
 * @TODO: normalize or add parameters for getQuery() +-> with starting "/"
 * @TODO: normalize or add parameters for getQueryString() +-> return array or string
 */
class CurrentUri extends Uri {
    
    /**
     * @var \Rf\Core\Uri\Uri Uri object which represent the current uri
     * @since 1.0
     */
    protected static $uri;

    /**
     * Set the current uri as an Uri object in $uri property
     *
     * @since 1.0
     *
     * @return void
     */
    public static function init() {
        self::$uri = new Uri(self::INIT_WITH_CURRENT_URI);
    }

    /**
     * Get the current uri object
     *
     * @since 1.0
     *
     * @return \Rf\Core\Uri\Uri
     */
    public static function getInstance() {
        if(!isset(self::$uri)) {
            self::init();
        }
        return self::$uri;
    }

    /**
     * Get the current uri protocol
     *
     * @since 1.0
     *
     * @return string
     */
    public static function getProtocol() {
        if(!isset(self::$uri)) {
            self::init();
        }
        return self::$uri->protocol();
    }

    /**
     * Get the current uri credentials
     *
     * @since 1.0
     *
     * @return array
     */
    public static function getCredentials() {
        if(!isset(self::$uri)) {
            self::init();
        }
        return self::$uri->credentials();
    }

    /**
     * Get the current uri host
     *
     * @since 1.0
     *
     * @return string
     */
    public static function getHost() {
        if(!isset(self::$uri)) {
            self::init();
        }
        return self::$uri->host();
    }

    /**
     * Get the current uri domain
     *
     * @return bool|string
     */
    public static function getDomain() {

        if(!isset(self::$uri)) {
            self::init();
        }

        return self::$uri->domain();

    }

    /**
     * Get the current uri port
     *
     * @since 1.0
     *
     * @return int
     */
    public static function getPort() {
        if(!isset(self::$uri)) {
            self::init();
        }
        return self::$uri->port();
    }

    /**
     * Get the current uri query
     *
     * @since 1.0
     *
     * @return string
     */
    public static function getQuery() {
        if(!isset(self::$uri)) {
            self::init();
        }
        return self::$uri->query();
    }

    /**
     * Get the current uri query params
     *
     * @since 1.0
     *
     * @return array
     */
    public static function getQueryString() {
        if(!isset(self::$uri)) {
            self::init();
        }
        return self::$uri->queryString();
    }

    /**
     * Get the full current uri
     *
     * @since 1.0
     *
     * @return string
     */
    public static function getFull() {
        if(!isset(self::$uri)) {
            self::init();
        }
        return self::$uri->full();
    }
    
}