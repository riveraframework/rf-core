<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Session\Sessions;

use Rf\Core\Session\Interfaces\SessionInterface;
use Rf\Core\Session\SessionService;

/**
 * Class PhpSession
 *
 * @package Rf\Core\Session\Sessions
 */
class PhpSession implements SessionInterface {

    /** @var string $sessionName Session name */
    protected $sessionName;

    /** @var string $name Session ID */
    protected $sessionId;

    /** @var int $sessionStatus Session status */
    protected $sessionStatus = 0;

    /** @var int $sessionStatus Session duration */
    protected $duration = 1440;

    /** @var array $data Used to temporary store values */
    protected $data = [];

    /**
     * Session constructor.
     *
     * @param string $sessionName
     * @param array $options
     */
    public function __construct($sessionName, $options = []) {

        $this->sessionName = $sessionName;

        if(!empty($options['duration'])) {

            $this->duration = $options['duration'];

        }

    }

    /**
     * Get session name
     *
     * @return string
     */
    public function getName() {

        return $this->sessionName;

    }

    /**
     * Get session ID
     *
     * @return string
     */
    public function getId() {

        return $this->sessionId;

    }

    /**
     * Start the session
     */
    public function start() {

        if($this->sessionStatus === 1) {
            // Already started
            return;
        }

        ini_set('session.name', $this->sessionName);

        ini_set('session.cookie_domain', SessionService::getConfig('domain'));
        ini_set('session.cookie_lifetime', $this->duration);
        ini_set('session.gc_maxlifetime', $this->duration);

        session_start();

        // Session started
        $this->sessionStatus = 1;

    }

    /**
     * Stop the session
     */
    public function stop() {

        session_write_close();

        // Session stopped
        $this->sessionStatus = 0;

    }

    /**
     * Get session item
     *
     * @param string $key
     *
     * @return mixed|null
     */
    public function get($key) {

        // Return the value from session request cache if already loaded
        if(isset($_SESSION[$key])) {

            return $_SESSION[$key];

        } else {

            return null;

        }

    }

    /**
     * Set an item in the session
     *
     * @param string $key
     * @param mixed $value
     * @param int $expiration
     */
    public function set($key, $value, $expiration) {

        $_SESSION[$key] = $value;

    }

    /**
     * Delete an item from the session
     *
     * @param string $key
     */
    public function delete($key) {

        if(isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }

    }

    /**
     * Destroy the session
     */
    public function destroy() {

        session_unset();
        session_destroy();

        // Session stopped
        $this->sessionStatus = 0;

    }

}