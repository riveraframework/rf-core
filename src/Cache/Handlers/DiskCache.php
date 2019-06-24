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
use Rf\Core\System\FileSystem\DirectoryFactory;
use Rf\Core\System\FileSystem\DiskPathWriter;

/**
 * Class DiskCache
 *
 * @package Rf\Core\Cache\Handlers
 */
class DiskCache extends DiskPathWriter implements CacheInterface {

    /**
     * Get the cache type
     *
     * @return string
     */
    public function getType() {

        return 'disk';

    }

    /**
     * Add an endpoint
     *
     * @param string $endpoint
     */
    public function addEndpoint($endpoint) {

        $this->addPath($endpoint);

    }

    /**
     * Get cache endpoints
     *
     * @return array
     */
    public function getEndpoints() {

        return $this->paths;

    }
    /**
     * Get value from disk cache
     *
     * @param string $key
     * @param int $expires
     *
     * @return string <p>
     * Returns a string or false on failure or if expired
     * </p>
     */
    public function get($key, $expires = 0) {

        foreach($this->paths as $path) {

            // Get the file cache path
            $cacheFilePath = $this->buildCachePath($path, $key);

            $cachedResponse = parent::getFile($cacheFilePath, $expires);

            if($cachedResponse) {
                return $cachedResponse;
            }

        }

        return false;

    }

    /**
     * Set value in disk cache
     *
     * @param string $key
     * @param string $value
     */
    public function set($key, $value) {

        foreach($this->paths as $path) {

            // Get the file cache path
            $cacheFilePath = $this->buildCachePath($path, $key);

            parent::writeFile($cacheFilePath, $value);

        }

    }

    /**
     * Delete value
     *
     * @param string $key
     */
    public function delete($key) {

        foreach($this->paths as $path) {

            // Get the file cache path
            $cacheFilePath = $this->buildCachePath($path, $key);

            parent::deleteFile($cacheFilePath);

        }

    }

    /**
     * Flush cache
     *
     * @throws \Exception
     */
    public function flush() {

        foreach($this->paths as $path) {

            parent::flushPath($path);

        }

    }

    /**
	 * Build the cache file path
	 *
	 * @param string $basePath
	 * @param string $key
	 *
	 * @return string
	 */
	protected function buildCachePath($basePath, $key) {

		$path = $basePath;
		$path .= DirectoryFactory::buildRelativePathFromString($key);
		$path .= $key . '.cache';

		return $path;

	}

}