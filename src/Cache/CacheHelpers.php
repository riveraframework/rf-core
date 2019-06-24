<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Cache {

    /**
     * Class CacheHelpers
     *
     * @package Rf\Core\Cache
     */
    class CacheHelpers {

        /**
         * This function goal is to use the Autoloader to load the current file containing
         * useful helpers (functions)
         */
        public static function init() { }

    }

}

namespace {

	use Rf\Core\Cache\CacheService;

	/**
	 * Get cache service
     *
     * @param string $name
	 *
	 * @return CacheService
     * @throws Exception
	 */
    function rf_cache($name = '') {

    	return rf_sp()->getCache($name);

    }

	/**
	 * Get value from cache
	 *
	 * @param string $key
	 * @param string[] $cacheIdentifiers
	 *
	 * @return string
     * @throws Exception
	 */
    function rf_cache_get($key, $cacheIdentifiers = []) {

        // Use default cache if no identifiers are provided
        if(empty($cacheIdentifiers)) {
            $cacheIdentifiers = [''];
        }

        // Look for the key in every requested cache service
        foreach($cacheIdentifiers as $cacheIdentifier) {

            // Try to get the value
            $cachedValue = rf_sp()->getCache($cacheIdentifier)->get($key);

            // Return the value if we found it
            if($cachedValue !== false) {
                return $cachedValue;
            }

        }

        return false;

    }

	/**
	 * Set value in cache(s)
	 *
	 * @param string $key
	 * @param string $value
	 * @param int $expires
	 * @param string[] $cacheIdentifiers
     *
     * @throws Exception
	 */
    function rf_cache_set($key, $value, $expires = 0, $cacheIdentifiers = []) {

        // Use default cache if no identifiers are provided
        if(empty($cacheIdentifiers)) {
            $cacheIdentifiers = [''];
        }

        // Look for the key in every requested cache service
        foreach($cacheIdentifiers as $cacheIdentifier) {

            // Try to get the value
            $cacheService = rf_sp()->getCache($cacheIdentifier);
            $cacheService->set($key, $value, $expires);

        }

    }

	/**
	 * Delete value
	 *
	 * @param string $key
	 * @param string[] $cacheIdentifiers
     *
     * @throws Exception
	 */
    function rf_cache_delete($key, $cacheIdentifiers = []) {

        // Use default cache if no identifiers are provided
        if(empty($cacheIdentifiers)) {
            $cacheIdentifiers = [''];
        }

        // Look for the key in every requested cache service
        foreach($cacheIdentifiers as $cacheIdentifier) {

            // Try to get the value
            $cacheService = rf_sp()->getCache($cacheIdentifier);
            $cacheService->delete($key);

        }

    }

	/**
	 * Flush all caches
     *
     * @throws Exception
	 */
    function rf_cache_flush_all() {

        // Use default cache if no identifiers are provided
        if(empty($cacheIdentifiers)) {
            $cacheIdentifiers = [''];
        }

        // Look for the key in every requested cache service
        foreach($cacheIdentifiers as $cacheIdentifier) {

            // Try to get the value
            $cacheService = rf_sp()->getCache($cacheIdentifier);
            $cacheService->flushAll();

        }

    }

}

