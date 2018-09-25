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
use Rf\Core\Cache\Exceptions\CacheConfigurationException;
use Rf\Core\Cache\Handlers\MemcachedCache;
use Rf\Core\Cache\Interfaces\CacheInterface;

/**
 * Class Cache
 *
 * @TODO: Handle cache strategy
 *
 * @package Rf\Core\Cache
 */
class CacheService {

	const HANDLER_TYPE_DISK = 'disk';
	const HANDLER_TYPE_MEMCACHE = 'memcache';
	const HANDLER_TYPE_MEMCACHED = 'memcached';
	const HANDLER_TYPE_REDIS = 'redis';

	/** @var CacheInterface[] $cacheHandlers  */
	protected $cacheHandlers = [];

	/**
	 * Memcache constructor.
	 *
	 * @param array $cacheConfig
	 *
	 * @throws CacheConfigurationException
	 * @throws \Exception
	 */
	public function __construct(array $cacheConfig) {

        if(!empty($cacheConfig['handlers'])) {

            foreach ($cacheConfig['handlers'] as $handlerIdentifier => $handlerConfig) {

                // Check if the handler type is authorized
                $handlerType = !empty($handlerConfig['type']) ? $handlerConfig['type'] : '';
                if (!in_array($handlerType, [
                    self::HANDLER_TYPE_DISK,
                    self::HANDLER_TYPE_MEMCACHE,
                    self::HANDLER_TYPE_MEMCACHED,
                ])) {
                    throw new CacheConfigurationException('Cache setup error: the cache type `' . $handlerType . '` does not exists');
                }

                switch ($handlerType) {

                    // Create Memcache handler
                    case self::HANDLER_TYPE_MEMCACHE:

                        $memcache = new MemcacheCache();
                        $memcache->setIdentifier($handlerIdentifier);

                        // Check if the Memcache server list is empty
                        $servers = $handlerConfig['servers'];
                        if (empty($servers)) {
                            throw new CacheConfigurationException('Cache setup error: the Memcache server list is empty');
                        }

                        // Add listed server to the Memcache handler
                        foreach ($servers as $server) {

                            if (empty($server['host']) || empty($server['port'])) {
                                throw new CacheConfigurationException('Cache setup error: the Memcache configuration is invalid');
                            }

                            $memcache->addServer($server['host'], $server['port']);

                        }

                        // Check that the memcached server support the common operations
                        if (!empty($handlerConfig['required'])) {
                            $memcache->checkService();
                        }

                        $this->cacheHandlers[] = $memcache;
                        break;

                    // Create Memcached handler
                    case self::HANDLER_TYPE_MEMCACHED:

                        $memcached = new MemcachedCache();
                        $memcached->setIdentifier($handlerIdentifier);

                        // Check if the Memcached server list is empty
                        $servers = $handlerConfig['servers'];
                        if (empty($servers)) {
                            throw new CacheConfigurationException('Cache setup error: the Memcached server list is empty');
                        }

                        // Add listed server to the Memcached handler
                        foreach ($servers as $server) {

                            if (empty($server['host']) || empty($server['port'])) {
                                throw new CacheConfigurationException('Cache setup error: the Memcached configuration is invalid');
                            }

                            $memcached->addServer($server['host'], $server['port']);

                        }

                        // Check that the memcached server support the common operations
                        if (!empty($handlerConfig['required'])) {
                            $memcached->checkService();
                        }

                        $this->cacheHandlers[] = $memcached;
                        break;

                    // Create disk handler
                    case self::HANDLER_TYPE_DISK:

                        // Create disk cache handler
                        $diskCache = new DiskCache();
                        $diskCache->setIdentifier($handlerIdentifier);
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
	 * @param string[] $cacheIdentifiers
	 *
	 * @return string
	 */
	public function get($key, $cacheIdentifiers = []) {

		foreach($this->cacheHandlers as $cacheHandler) {

			if(empty($cacheIdentifier) || in_array($cacheHandler->getIdentifier(), $cacheIdentifiers)) {

				$value = $cacheHandler->get($key);

				if($value !== false) {
					return $value;
				}

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
	 * @param string[] $cacheIdentifiers
	 */
	public function set($key, $value, $expires = 0, $cacheIdentifiers = []) {

		foreach($this->cacheHandlers as $cacheHandler) {

			if(!empty($cacheIdentifiers) && !in_array($cacheHandler->getIdentifier(), $cacheIdentifiers)) {
				continue;
			} else {
				$cacheHandler->set($key, $value, $expires);
			}

		}

	}

	/**
	 * Delete value
	 *
	 * @param string $key
	 * @param string[] $cacheIdentifiers
	 */
	public function delete($key, $cacheIdentifiers = []) {

		foreach($this->cacheHandlers as $cacheHandler) {

			if(!empty($cacheIdentifiers) && !in_array($cacheHandler->getIdentifier(), $cacheIdentifiers)) {
				continue;
			} else {
				$cacheHandler->delete($key);
			}

		}

	}

	/**
	 * Flush all caches
	 *
	 * @param string[] $cacheIdentifiers
	 */
	public function flushAll($cacheIdentifiers = []) {

		foreach($this->cacheHandlers as $cacheHandler) {
			if(!empty($cacheIdentifiers) && !in_array($cacheHandler->getIdentifier(), $cacheIdentifiers)) {
				continue;
			} else {
				$cacheHandler->flush();
			}

		}

	}

}