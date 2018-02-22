<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Cache\Handlers;

/**
 * Class MemcacheCache
 *
 * @package Rf\Core\Cache\Handlers
 */
class MemcacheCache extends DefaultCache {

	protected $memcache;

	/**
	 * MemcacheCache constructor.
	 */
	public function __construct() {

		if(!class_exists('\Memcache')) {
			throw new \Exception('Memcache is not configured on this server.');
		}

		$this->memcache = new \Memcache();

	}

    /**
     * Get memcache
     *
     * @return \Memcache
     */
    public function getMemcache() {

        return $this->memcache;

    }

	/**
	 * Add a server
	 *
	 * @param string $host
	 * @param string $port
	 */
	public function addServer($host, $port) {

		$this->memcache->addServer($host, $port);

	}

    /**
     * Check if the write/read operations work
     *
     * @throws \Exception
     */
    public function checkService() {

        $check = $this->memcache->get('memcached-check');

        if(!$check) {

            $this->memcache->set('memcached-check', 1, 3600);
            $check = $this->memcache->get('memcached-check');

        }

        if(!$check) {
            throw new \Exception('The Memcached servers are not accessible.');
        }

    }

	/**
	 * Get value
	 *
	 * @param string $key
	 *
	 * @return string
	 */
	public function get($key) {

		return $this->memcache->get($key);

	}

	/**
	 * Set value
	 *
	 * @param string $key
	 * @param string $value
	 * @param int $expires
	 */
	public function set($key, $value, $expires = 0) {

		$this->memcache->set($key, $value, null, $expires);

	}

	/**
	 * Delete value
	 *
	 * @param string $key
	 */
	public function delete($key) {

		$this->memcache->delete($key);

	}

	/**
	 * Flush cache
	 */
	public function flush() {

		$this->memcache->flush();

	}

}