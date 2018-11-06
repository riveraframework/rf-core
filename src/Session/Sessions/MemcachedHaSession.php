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

use Rf\Core\Cache\Handlers\MemcachedCache;
use Rf\Core\Security\Uuid;
use Rf\Core\Session\Cookie;
use Rf\Core\Session\Interfaces\SessionInterface;

/**
 * Class MemcachedHaSession
 *
 * @TODO: Support flexible map
 *
 * @package Rf\Core\Session\Sessions
 */
class MemcachedHaSession implements SessionInterface {

    const MAP_TYPE_STATIC = 'static';
    const MAP_TYPE_DYNAMIC = 'dynamic';
    const MAP_TYPE_FLEXIBLE = 'flexible';

    /** @var string $sessionName Session name */
    protected $sessionName;

    /** @var string $name Session ID */
    protected $sessionId;

    /** @var int $sessionStatus Session status */
    protected $sessionStatus = 0;

    /** @var int $sessionStatus Session duration */
    protected $duration = 1440;

    /** @var MemcachedCache $handler Session handler */
    protected $handler;

    /** @var array $staticMap Structure of the session */
    protected $map = [];

    /** @var string $mapType */
    protected $mapType;

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

        if(!empty($options['map'])) {

            $this->map = $options['map'];
            $this->mapType = self::MAP_TYPE_STATIC;

        }

        if(!empty($options['handler'])) {

            $this->handler = $options['handler'];

        }

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
     * Add a handler
     *
     * @param MemcachedCache $handler
     */
    public function setHandler(MemcachedCache $handler) {

        $this->handler = $handler;

    }

    /**
     * Set a static map
     *
     * @param array $map
     */
    public function setStaticMap(array $map) {

        $this->map = $map;
        $this->mapType = self::MAP_TYPE_STATIC;

    }

    /**
     * Get map
     *
     * @return array|mixed
     */
    public function getMap() {

        if(!empty($this->map)) {

            return $this->map;

        } else {

            return [];

        }

    }

    /**
     * Start the session
     *
     * @TODO: Handle disabled cookies
     *
     * @throws \Exception
     */
    public function start() {

        if($this->sessionStatus === 1) {
            // Already started
            return;
        }

        $isNew = false;
        if(!isset($this->sessionId)) {

            // Check the cookie
            $sessionId = Cookie::cookieExist($this->sessionName) ? $_COOKIE[$this->sessionName] : false;

            // If cookie not created --> generate session ID and create cookie
            if(empty($sessionId)) {

                $isNew = true;

                // Generate session ID
                // @TODO: Update
                $sessionId = Uuid::generateUuidV4();

                // Create the cookie
                $cookie = new Cookie(
                    $this->sessionName,
                    $sessionId,
                    $this->duration
                );
                $cookie->create();

            }

            // Set the session ID
            $this->sessionId = $sessionId;

        }

        if(
            !$isNew
            && empty($this->staticMap)
            && empty($this->dynamicMap)
        ) {

            // @TODO: Load dynamic map
            $this->mapType = self::MAP_TYPE_DYNAMIC;

            // Exception?
            // Or option to try to recover?

        }

        if(empty($this->sessionId)) {

            throw new \Exception('Session ID not configured');

        }

        if(rf_empty($this->getMap())) {

            throw new \Exception('Map not configured');

        }

        // Session started
        $this->sessionStatus = 1;

    }

    /**
     * Stop the session
     */
    public function stop() {

        unset($this->sessionId);
        unset($this->data);

        if($this->mapType !== self::MAP_TYPE_STATIC) {

            unset($this->mapType);
            unset($this->map);

        }

        // Session stopped
        $this->sessionStatus = 0;

    }

    /**
     * Get full item key
     *
     * @param string $key
     *
     * @return string
     */
    protected function getFullKey($key) {

        return $this->sessionName . '::' . $this->sessionId . '::' . $key;

    }

    /**
     * Get session item
     *
     * @param string $key
     *
     * @return mixed|null|string
     * @throws \Exception
     */
    public function get($key) {

        // Return the value from session request cache if already loaded
        if(isset($this->data[$key])) {

            return $this->data[$key];

        }

        // Get value from first handler
        $value = null;
        try {

            $value = $this->handler->get($this->getFullKey($key));
            $this->data[$key] = $value;

        } catch (\Exception $e) {

            // @TODO: Log error

        }

        // @TODO: Sync data in handler
        // Use system in link for background process
        // $this->sync($key, $value, $notPresentIn);

        if(isset($value)) {

            return $value;

        } else {

            throw new \Exception('Session not found', 'SESSION_NOT_FOUND');

        }


    }

    /**
     * Set an item in the session
     *
     * @param string $key
     * @param mixed $value
     * @param int $expiration
     *
     * @throws \Exception
     */
    public function set($key, $value, $expiration) {

        $map = $this->getMap();

        // Prevent setting the key if not authorized in map
        if(!in_array($key, $map)) {

            throw new \Exception('Not authorized to set this key');

        }

        // Set key/value for each handler
        $this->handler->set($this->getFullKey($key), $value, $expiration);

    }

    /**
     * Delete an item from the session
     *
     * @param string $key
     */
    public function delete($key) {

        // Remove key for each handler
        $this->handler->delete($this->getFullKey($key));

    }

    /**
     * Destroy the session
     */
    public function destroy() {

        $this->stop();

        Cookie::deleteCookie($this->sessionName);

        // @TODO: Create method to remove by prefix in cache

        // Remove every key for each handler
        foreach($this->getMap() as $key) {

            $this->handler->delete($key);

        }

        unset($this->sessionName);

        // Session stopped
        $this->sessionStatus = 0;

    }

}