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
 * Class MemcachedCache
 *
 * @package Rf\Core\Cache\Handlers
 */
class MemcachedCache extends DefaultCache {

	/** @var \Memcached $memcached */
	protected $memcached;

	/**
	 * MemcacheCache constructor.
	 */
	public function __construct() {

		if(!class_exists('\Memcached')) {
			throw new \Exception('Memcached is not configured on this server.');
		}

		$this->memcached = new \Memcached();

	}

	/**
	 * Get memcached
	 *
	 * @return \Memcached
	 */
	public function getMemcached() {

		return $this->memcached;

	}

	/**
	 * Add a server
	 *
	 * @param string $host
	 * @param string $port
	 */
	public function addServer($host, $port) {

		$this->memcached->addServer($host, $port);

	}

	/**
	 * Check if the write/read operations work
	 *
	 * @throws \Exception
	 */
	public function checkService() {

		$check = $this->memcached->get('memcached-check');

		if(!$check) {

			$this->memcached->set('memcached-check', 1, 3600);
			$check = $this->memcached->get('memcached-check');

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

		return $this->memcached->get($key);

	}

	/**
	 * Set value
	 *
	 * @param string $key
	 * @param string $value
	 * @param int $expires
	 */
	public function set($key, $value, $expires = 0) {

		$this->memcached->set($key, $value, $expires);

	}

	/**
	 * Delete value
	 *
	 * @param string $key
	 */
	public function delete($key) {

		$this->memcached->delete($key);

	}

	/**
	 * Flush cache
	 */
	public function flush() {

		$this->memcached->flush();

	}

    /**
     * Get cache stats
     *
     * @return array
     */
    public function getStats() {

        return $this->memcached->getStats();

    }

}