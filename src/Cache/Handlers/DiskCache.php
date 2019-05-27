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

use Rf\Core\Core\Core;
use Rf\Core\System\AvailableFunctions;
use Rf\Core\System\FileSystem\DirectoryFactory;

/**
 * Class DiskCache
 *
 * @package Rf\Core\Cache\Handlers
 */
class DiskCache extends DefaultCache {

    /** @var string $type */
    protected $type = 'disk';

	/**
	 * DiskCache constructor.
	 */
	public function __construct() {}

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

		// Get the file cache path
		$cacheFilePath = $this->buildCachePath($key);

		if(file_exists($cacheFilePath) && (filemtime($cacheFilePath) + $expires > time())) {
			return file_get_contents($cacheFilePath);
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

		// Get the file cache path
		$cacheFilePath = $this->buildCachePath($key);

		if(!is_dir(dirname($cacheFilePath))) {
			mkdir(dirname($cacheFilePath), 0775, true);
		}

		file_put_contents($cacheFilePath, $value);

	}

	/**
	 * Delete value
	 *
	 * @param string $key
	 */
	public function delete($key) {

		// Get the file cache path
		$cacheFilePath = $this->buildCachePath($key);

		if(file_exists($cacheFilePath)) {
			unlink($cacheFilePath);
		}

	}

	/**
	 * Flush cache
	 */
	public function flush() {

		if(AvailableFunctions::isShellExecEnabled() === true) {

			$cmd = 'rm -rf ' . rf_dir('cache') . '/*';
			shell_exec($cmd);

		} else {

            rf_unlink(rf_dir('cache'), true);

		}

	}

    /**
     * Get cache stats
     *
     * @return array
     */
	public function getStats() {

	    // @TODO: Add different paths for the disk cache handler (equiv. of memcached servers)
	    // @TODO: Count files and folders
	    return [
	        // Return stats for each path
        ];

    }

    /**
	 * Build the cache file path
	 *
	 * @param string $key
	 *
	 * @return string
	 */
	protected function buildCachePath($key) {

		$path = rf_dir('cache');
		$path .= DirectoryFactory::buildRelativePathFromString($key);
		$path .= $key . '.cache';

		return $path;

	}

}