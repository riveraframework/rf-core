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

	/**
	 * MemcacheWrapper constructor.
	 *
	 * @throws \Exception
	 */
    public function __construct()
    {

        if(!class_exists('\Memcache')) {
            throw new \Exception('Memcache is not configured on this server.');
        }

    	$this->service = new \Memcache();

    }

    /**
     * Get the Memcache service
     *
     * @return \Memcache
     */
    public function getService() {

        return $this->service;

    }

}