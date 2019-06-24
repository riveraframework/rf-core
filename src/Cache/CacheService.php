<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Cache;

use Rf\Core\Cache\Handlers\DiskCache;
use Rf\Core\Cache\Handlers\MemcacheCache;
use Rf\Core\Cache\Exceptions\CacheConfigException;
use Rf\Core\Cache\Handlers\MemcachedCache;
use Rf\Core\Cache\Interfaces\CacheInterface;
use Rf\Core\Log\LogService;
use Rf\Core\Service\Service;

/**
 * Class Cache
 *
 * @TODO: Handle cache strategy
 *
 * @package Rf\Core\Cache
 */
class CacheService extends Service {

    const TYPE = 'cache';

    const HANDLER_TYPE_DISK = 'disk';
    const HANDLER_TYPE_MEMCACHE = 'memcache';
    const HANDLER_TYPE_MEMCACHED = 'memcached';
    const HANDLER_TYPE_REDIS = 'redis';

    /** @var CacheConfiguration */
    protected $configuration;

    /** @var CacheInterface[] $cacheHandlers  */
    protected $cacheHandlers = [];

    /**
     * Load the cache configuration
     *
     * @param array $configuration
     *
     * @throws CacheConfigException
     * @throws \Exception
     */
    public function loadConfiguration(array $configuration) {

        $this->configuration = new CacheConfiguration($configuration);

        // Get handlers
        $handlers = $this->configuration->getHandlers();

        if(!empty($handlers)) {

            foreach ($handlers as $handlerIdentifier => $handlerConfig) {

                // Check if the handler type is authorized
                $handlerType = !empty($handlerConfig['type']) ? $handlerConfig['type'] : '';
                if (!in_array($handlerType, [
                    self::HANDLER_TYPE_DISK,
                    self::HANDLER_TYPE_MEMCACHE,
                    self::HANDLER_TYPE_MEMCACHED,
                ])) {
                    throw new CacheConfigException(LogService::TYPE_ERROR, 'Cache setup error: the cache type `' . $handlerType . '` does not exists');
                }

                switch ($handlerType) {

                    // Create Memcache handler
                    case self::HANDLER_TYPE_MEMCACHE:
                    case self::HANDLER_TYPE_MEMCACHED:

                        if($handlerType === self::HANDLER_TYPE_MEMCACHE) {
                            $handler = new MemcacheCache(!empty($handlerConfig['options']) ? $handlerConfig['options'] : []);
                        } else {
                            $handler = new MemcachedCache(!empty($handlerConfig['options']) ? $handlerConfig['options'] : []);
                        }

                        // Check if the Memcached server list is empty
                        $servers = $handlerConfig['servers'];
                        if (empty($servers)) {
                            throw new CacheConfigException(LogService::TYPE_ERROR, 'Cache setup error: the Memcached server list is empty');
                        }

                        // Add listed server to the Memcached handler
                        foreach ($servers as $server) {

                            if (empty($server['host']) || empty($server['port'])) {
                                throw new CacheConfigException(LogService::TYPE_ERROR, 'Cache setup error: the Memcached configuration is invalid');
                            }

                            $handler->addServer($server['host'], $server['port']);

                        }

                        // Check that the memcached server support the common operations
                        if (!empty($handlerConfig['required'])) {
                            $handler->checkService();
                        }

                        $this->cacheHandlers[] = $handler;
                        break;

                    // Create disk handler
                    case self::HANDLER_TYPE_DISK:

                        // Create disk cache handler
                        $diskCache = new DiskCache(!empty($handlerConfig['options']) ? $handlerConfig['options'] : []);
                        $this->cacheHandlers[] = $diskCache;
                        break;

                }

            }

        }

    }

    /**
     * Get available cache handlers
     *
     * @return CacheInterface[]
     */
    public function getHandlers() {

        return $this->cacheHandlers;

    }

    /**
     * Get value from cache
     *
     * @param string $key
     *
     * @return string
     */
    public function get($key) {

        foreach($this->cacheHandlers as $cacheHandler) {

            $value = $cacheHandler->get($key);

            if($value !== false) {
                return $value;
            }

        }

        return null;

    }

    /**
     * Set value in cache(s)
     *
     * @TODO: Fix the expire params (not common with disk and memory)
     *
     * @param string $key
     * @param string $value
     * @param int $expires
     */
    public function set($key, $value, $expires = 0) {

        foreach($this->cacheHandlers as $cacheHandler) {

            $cacheHandler->set($key, $value, $expires);

        }

    }

    /**
     * Delete value
     *
     * @param string $key
     */
    public function delete($key) {

        foreach($this->cacheHandlers as $cacheHandler) {

            $cacheHandler->delete($key);

        }

    }

    /**
     * Flush all caches
     */
    public function flushAll() {

        foreach($this->cacheHandlers as $cacheHandler) {

            $cacheHandler->flush();

        }

    }

    /**
     * Generate a basic cache key
     *
     * @param string[] $params
     *
     * @return string
     */
    public static function generateBasicCacheKey(array $params) {

        return md5(implode('-', $params));

    }

    /**
     * Generate a basic cache key with a prefix
     *
     * @param string $prefix
     * @param string[] $params
     *
     * @return string
     */
    public static function generateBasicCacheKeyWithPrefix($prefix, array $params) {

        return $prefix . md5(implode('-', $params));

    }

}