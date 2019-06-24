<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\System\FileSystem;

use Rf\Core\System\AvailableFunctions;

/**
 * Class DiskPathWriter
 *
 * @package Rf\Core\System\FileSystem
 */
class DiskPathWriter {

    /** @var array $options */
    protected $options;

    /** @var array */
    protected $paths = [];

    /**
     * DiskPathWriter constructor.
     *
     * @param array $options
     *
     * @throws \Exception
     */
    public function __construct(array $options = []) {

        // Set options
        $this->options = $options;

    }

    /**
     * Add a path
     *
     * @param string $path
     */
    public function addPath($path) {

        $this->paths[] = $path;

    }

    /**
     * Get file content
     *
     * @param string $filePath
     * @param int $expires
     *
     * @return string <p>
     * Returns a string or false on failure or if expired
     * </p>
     */
    public function getFile($filePath, $expires = 0) {

        if(file_exists($filePath) && (filemtime($filePath) + $expires > time())) {
            // @TODO: Use File class
            return file_get_contents($filePath);
        }

        return false;

    }

    /**
     * Create a file with the specified content
     *
     * @param string $filePath
     * @param string $content
     */
    public function writeFile($filePath, $content) {

        if(!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0775, true);
        }

        // @TODO: Use File class
        file_put_contents($filePath, $content);

    }

    /**
     * Delete value
     *
     * @param string $filePath
     */
    public function deleteFile($filePath) {

        if(file_exists($filePath)) {
            unlink($filePath);
        }

    }

    /**
     * Flush cache
     *
     * @param string $path
     *
     * @throws \Exception
     */
    public function flushPath($path) {

        if(AvailableFunctions::isShellExecEnabled() === true) {

            $cmd = 'rm -rf ' . $path . '/*';
            shell_exec($cmd);

        } else {

            rf_unlink($path, true);

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

}