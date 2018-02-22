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

	/** @var CacheInterface[] $caches  */
	protected $caches = [];

	/**
	 * Memcache constructor.
	 *
	 * @param array $cacheConfig
	 *
	 * @throws CacheConfigurationException
	 */
	public function __construct(array $cacheConfig) {

		foreach($cacheConfig['handlers'] as $handlerIdentifier => $handlerConfig) {

			// Check if the handler type is authorized
			$handlerType = !empty($handlerConfig['type']) ? $handlerConfig['type'] : '';
			if(!in_array($handlerType, [
				self::HANDLER_TYPE_DISK,
				self::HANDLER_TYPE_MEMCACHE,
				self::HANDLER_TYPE_MEMCACHED,
			])) {
				throw new CacheConfigurationException('Cache setup error: the cache type `' . $handlerType . '` does not exists');
			}

			switch($handlerType) {

				// Create Memcache handler
				case self::HANDLER_TYPE_MEMCACHE:

					$memcache = new MemcacheCache();
					$memcache->setIdentifier($handlerIdentifier);

					// Check if the Memcache server list is empty
					$servers = $handlerConfig['servers'];
					if(empty($servers)) {
						throw new CacheConfigurationException('Cache setup error: the Memcache server list is empty');
					}

					// Add listed server to the Memcache handler
					foreach($servers as $server) {

						if(empty($server['host']) || empty($server['port'])) {
							throw new CacheConfigurationException('Cache setup error: the Memcache configuration is invalid');
						}

						$memcache->addServer($server['host'], $server['port']);

					}

                    // Check that the memcached server support the common operations
                    $memcache->checkService();

					$this->caches[] = $memcache;
					break;

                // Create Memcached handler
                case self::HANDLER_TYPE_MEMCACHED:

                    $memcached = new MemcachedCache();
                    $memcached->setIdentifier($handlerIdentifier);

                    // Check if the Memcached server list is empty
                    $servers = $handlerConfig['servers'];
                    if(empty($servers)) {
                        throw new CacheConfigurationException('Cache setup error: the Memcached server list is empty');
                    }

                    // Add listed server to the Memcached handler
                    foreach($servers as $server) {

                        if(empty($server['host']) || empty($server['port'])) {
                            throw new CacheConfigurationException('Cache setup error: the Memcached configuration is invalid');
                        }

                        $memcached->addServer($server['host'], $server['port']);

                    }

                    // Check that the memcached server support the common operations
                    $memcached->checkService();

                    $this->caches[] = $memcached;
                    break;

				// Create disk handler
				case self::HANDLER_TYPE_DISK:

					// Create disk cache handler
					$diskCache = new DiskCache();
					$diskCache->setIdentifier($handlerIdentifier);
					$this->caches[] = $diskCache;
					break;

			}

		}

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

		foreach($this->caches as $cache) {

			if(empty($cacheIdentifier) || in_array($cache->getIdentifier(), $cacheIdentifiers)) {

				$value = $cache->get($key);

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

		foreach($this->caches as $cache) {

			if(!empty($cacheIdentifiers) && !in_array($cache->getIdentifier(), $cacheIdentifiers)) {
				continue;
			} else {
				$cache->set($key, $value, $expires);
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

		foreach($this->caches as $cache) {

			if(!empty($cacheIdentifiers) && !in_array($cache->getIdentifier(), $cacheIdentifiers)) {
				continue;
			} else {
				$cache->delete($key);
			}

		}

	}

	/**
	 * Flush all caches
	 *
	 * @param string[] $cacheIdentifiers
	 */
	public function flushAll($cacheIdentifiers = []) {

		foreach($this->caches as $cache) {
			if(!empty($cacheIdentifiers) && !in_array($cache->getIdentifier(), $cacheIdentifiers)) {
				continue;
			} else {
				$cache->flush();
			}

		}

	}

}