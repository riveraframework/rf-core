<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Mvc;

use Rf\Core\Core\Core;

/**
 * Class Cache
 *
 * @package Rf\Core\Mvc
 *
 * @TODO: Remove
 */
abstract class Cache {

    /**
     * Get the content of a cached file
     *
     * @param string $fileName File name inside the cache folder
     * @param int $maxDuration Maximum cache file duration in minutes
     *
     * @return string|bool
     */
    public static function get(string $fileName, $maxDuration = 0) {

        $filePath = rf_dir('cache') . $fileName;

        if(file_exists($filePath) && is_readable($filePath) && ($maxDuration < 1 || filemtime($filePath) > time() - $maxDuration * 60)) {
            return file_get_contents($filePath);
        } else {
            return false;
        }

    }

	/**
	 * Write data in cache
	 *
	 * @param string $fileName
	 * @param string $content
	 *
	 * @return int
	 */
    public static function write(string $fileName, string $content) {

	    $filePath = rf_dir('cache') . $fileName;

	    // Create the directory if it does not exists
	    if (!is_dir(dirname($filePath))) {
		    mkdir(dirname($filePath), 0755, true);
	    }

	    return file_put_contents($filePath, $content);

    }
    
    /**
     * Empty the cache
     */
    public static function emptyCache() {

        rf_unlink(rf_dir('cache'), true);

    }

}
