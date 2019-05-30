<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Wrappers\InternalServices\Memcached;

/**
 * Class MemcachedWrapper
 *
 * @package Rf\Core\Wrappers\InternalServices\Memcached
 */
class MemcachedWrapper {

    /** @var \Memcached  */
	protected $service;

	/**
	 * MemcachedWrapper constructor.
	 *
	 * @throws \Exception
	 */
    public function __construct()
    {

        if(!class_exists('\Memcached')) {
            throw new \Exception('Memcached is not configured on this server.');
        }

    	$this->service = new \Memcached();

    }

    /**
     * Get the Memcached service
     *
     * @return \Memcached
     */
    public function getService() {

        return $this->service;

    }

}