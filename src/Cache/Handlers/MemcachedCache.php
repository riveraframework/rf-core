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

use Rf\Core\Cache\Interfaces\CacheInterface;
use Rf\Core\Wrappers\InternalServices\Memcached\MemcachedWrapper;

/**
 * Class MemcachedCache
 *
 * @package Rf\Core\Cache\Handlers
 */
class MemcachedCache extends MemcachedWrapper implements CacheInterface {

    /**
     * Get the cache type
     *
     * @return string
     */
    public function getType() {

        return 'memcached';

    }

    /**
     * Add an endpoint
     *
     * @param string $endpoint
     */
    public function addEndpoint($endpoint) {

        list($host, $port) = explode(':', $endpoint);

        $this->addServer($host, $port);

    }

    /**
     * Get cache endpoints
     *
     * @return array
     */
    public function getEndpoints() {

        return $this->servers;

    }

}