<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Session;

use Rf\Core\Base\ObjectTrait;
use Rf\Core\Uri\CurrentUri;
use Rf\Core\Exception\BaseException;

/**
 * Class Cookie
 *
 * @since 1.0
 *
 * @package Rf\Core\Session
 */
class Cookie {

    use ObjectTrait;

    /** @var string $name Name of the cookie*/
    public $name;

    /** @var string|int $content Content of the cookiem */
    public $content;

    /** @var int $validity Timestamp until when the cookie is considered as valid */
    public $validity;

    /** @var string $url Domain of application of the cookie */
    public $url;

    /** @var bool $crossSubDomain Allow to use the wildcard to get the cookie cross domains */
    public $crossSubDomain;

    /**
     * Create a Cookie object with the necessary properties
     *
     * @param string $cookieName Name of the cookie
     * @param string|int $content Content of the cookie
     * @param int $validity Timestamp until when the cookie is considered as valid
     * @param bool $crossSubDomain
     */
    public function __construct($cookieName, $content = 0, $validity = null, $crossSubDomain = false) {

        $this->name = $cookieName;
        $this->content = $content;

        if(!isset($validity)) {
            $this->validity = time() + 60 * 60 * 24 * 7;
        } else {
            $this->validity = $validity;
        }
        $this->crossSubDomain = $crossSubDomain;
        $this->url = ($crossSubDomain ? '.' : '') . CurrentUri::getDomain();

    }

    /**
     * Create a cookie
     *
     * @throws \Exception
     */
    public function createCookie() {

        if(!setcookie($this->name, $this->content, $this->validity, '/', $this->url)) {

            throw new \Exception('Unable to create the cookie: '.$this->name);

        }

    }

    /**
     * Delete a cookie
     *
     * @param string $cookieName Name of the cookie
     */
    public static function deleteCookie($cookieName) {

        setcookie($cookieName, 0, time() - 30, '/', CurrentUri::getDomain());

    }

    /**
     * Check if a cookie exist
     *
     * @param string $cookieName Name of the cookie
     *
     * @return bool
     */
    public static function cookieExist($cookieName) {

        return isset($_COOKIE[$cookieName]) ? true : false;

    }

}