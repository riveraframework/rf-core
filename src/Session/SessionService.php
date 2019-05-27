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

use Rf\Core\Session\Interfaces\SessionInterface;
use Rf\Core\Session\Sessions\MemcachedHaSession;
use Rf\Core\Session\Sessions\PhpSession;

/**
 * Class SessionService
 *
 * @package Rf\Core\Session
 */
class SessionService {

    /** @var array  */
    protected $sessions = [];

    /**
     * Add a session
     *
     * @param SessionInterface $session
     */
    public function add(SessionInterface $session) {

        $this->sessions[$session->getName()] = $session;

    }

    /**
     * Get a session
     *
     * @param string $name
     * @return SessionInterface|PhpSession|MemcachedHaSession
     * @throws \Exception
     */
    public function get($name) {

        if(isset($this->sessions[(string)$name])) {

            return $this->sessions[(string)$name];

        } else {

            throw new \Exception('Session not found');

        }

    }

    /**
     * Remove a session (does not destroy the session)
     *
     * @param string $name
     */
    public function remove($name) {

        if(isset($this->sessions[(string)$name])) {
            unset($this->sessions[(string)$name]);
        }

    }

    /**
     * Get the session cookie config
     * @link: http://php.net/manual/fr/function.session-get-cookie-params.php
     *
     * @param null|string $key
     *
     * @return array|string|null
     */
    public static function getConfig($key = null) {

        $config = session_get_cookie_params();

        if(!isset($key)) {

            return $config;

        }

        if(isset($config[$key])) {

            return $config[$key];

        }

        return null;

    }

}