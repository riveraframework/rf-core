<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Wrappers\InternalServices\Memcache;

/**
 * Class MemcacheWrapper
 *
 * @package Rf\Core\Wrappers\InternalServices\Memcache
 */
class MemcacheWrapper {

    /** @var \Memcache  */
	protected $service;

    /** @var array $options */
    protected $options;

    /** @var array  */
    protected $servers = [];

	/**
	 * MemcacheWrapper constructor.
	 *
	 * @throws \Exception
	 */
    public function __construct(array $options = [])
    {

        if(!class_exists('\Memcache')) {
            throw new \Exception('Memcache is not configured on this server.');
        }

    	$this->service = new \Memcache();

        // Set options
        $this->options = $options;

    }

    /**
     * Get the Memcache service
     *
     * @return \Memcache
     */
    public function getService() {

        return $this->service;

    }


    /**
     * Add a server
     *
     * @param string $host
     * @param string $port
     */
    public function addServer($host, $port) {

        $this->getService()->addServer($host, $port);

        $this->servers[] = $host . ':' . $port;

    }

    /**
     * Check if the write/read operations work
     *
     * @throws \Exception
     */
    public function checkService() {

        $check = $this->getService()->get('memcached-check');

        if(!$check) {

            $this->getService()->set('memcached-check', 1, 3600);
            $check = $this->getService()->get('memcached-check');

        }

        if(!$check) {
            throw new \Exception('The Memcached servers are not accessible.');
        }

    }

    /**
     * Get value
     *
     * @param string $key
     * @param int $expires
     *
     * @return string
     */
    public function get($key, $expires = 0) {

        return $this->getService()->get($key);

    }

    /**
     * Set value
     *
     * @param string $key
     * @param string $value
     * @param int $expires
     */
    public function set($key, $value, $expires = 0) {

        $this->getService()->set($key, $value, null, $expires);

    }

    /**
     * Delete value
     *
     * @param string $key
     */
    public function delete($key) {

        $this->getService()->delete($key);

    }

    /**
     * Flush cache
     */
    public function flush() {

        $this->getService()->flush();

    }

    /**
     * Get cache stats
     *
     * @return array
     */
    public function getStats() {

        return $this->getService()->getExtendedStats();

    }

}