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

	use Rf\Core\Application\Application;
	use Rf\Core\Cache\CacheService;

	/**
	 * Get cache service
	 *
	 * @return CacheService
	 */
    function rf_cache() {

    	$cacheService = Application::getInstance()->getCacheService();
    	if(empty($cacheService)) {
    		return null;
	    } else {
    		return $cacheService;
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
    function rf_cache_get($key, $cacheIdentifiers = []) {

    	$cacheService = Application::getInstance()->getCacheService();
    	if(empty($cacheService)) {
    		return false;
	    } else {
    		return $cacheService->get($key, $cacheIdentifiers);
	    }

    }

	/**
	 * Set value in cache(s)
	 *
	 * @param string $key
	 * @param string $value
	 * @param int $expires
	 * @param string[] $cacheIdentifiers
	 */
    function rf_cache_set($key, $value, $expires = 0, $cacheIdentifiers = []) {

    	$cacheService = Application::getInstance()->getCacheService();
    	if(!empty($cacheService)) {
    		$cacheService->set($key, $value, $expires, $cacheIdentifiers);
	    }

    }

	/**
	 * Delete value
	 *
	 * @param string $key
	 * @param string[] $cacheIdentifiers
	 */
    function rf_cache_delete($key, $cacheIdentifiers = []) {

    	$cacheService = Application::getInstance()->getCacheService();
    	if(!empty($cacheService)) {
    		$cacheService->delete($key, $cacheIdentifiers);
	    }

    }

	/**
	 * Flush all caches
	 */
    function rf_cache_flush_all() {

    	$cacheService = Application::getInstance()->getCacheService();
    	if(!empty($cacheService)) {
    		$cacheService->flushAll();
	    }

    }

}

