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

/**
 * Class Cookie
 *
 * @package Rf\Core\Session
 */
class Cookie {

    use ObjectTrait;

    /** @var string $name Name of the cookie*/
    public $name;

    /** @var string|int $content Content of the cookiem */
    public $content;

    /** @var int $expiration Timestamp until when the cookie is considered as valid */
    public $expiration;

    /** @var string $domain Domain of application of the cookie */
    public $domain;

    /**
     * Create a Cookie object with the necessary properties
     *
     * @param string $cookieName Name of the cookie
     * @param string|int $content Content of the cookie
     * @param int $expiration Timestamp until when the cookie is considered as valid
     * @param string $domain
     */
    public function __construct($cookieName, $content = 0, $expiration = null, $domain = null) {

        $this->name = $cookieName;
        $this->content = $content;

        if(!isset($expiration)) {
            $this->expiration = time() + 60 * 60 * 24 * 7;
        } else {
            $this->expiration = $expiration;
        }
        $this->domain = isset($domain) ? $domain : SessionService::getConfig('domain');

    }

    /**
     * Create a cookie
     *
     * @throws \Exception
     */
    public function create() {

        if(!setcookie($this->name, $this->content, $this->expiration, '/', $this->domain)) {

            throw new \Exception('Unable to create the cookie: '.$this->name);

        }

    }

    /**
     * Delete a cookie
     *
     * @param string $cookieName Name of the cookie
     */
    public static function deleteCookie($cookieName) {

        setcookie($cookieName, 0, time() - 30);

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