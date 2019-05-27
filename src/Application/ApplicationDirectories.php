<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Application;

/**
 * Class ApplicationDirectories
 *
 * @package Rf\Core\Configuration
 */
class ApplicationDirectories {

    /**
     * @var string[string] Application additional directory paths
     */
    public $dirs = [];

    /**
     * Create the directory object and initiate all directories
     */
    public function __construct() {

        $basePath = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/';
        $appPath = $basePath . 'app/';

        $this->dirs = [
            'app'      => $appPath,
            'base'     => $basePath,
            'config'   => $appPath . 'config/',
            'cache'    => $appPath . 'cache/',
            'classes'  => $appPath . 'classes/',
            'core'     => $appPath . 'core/',
            'entities' => $appPath . 'entities/',
            'lang'     => $appPath . 'lang/',
            'libs'     => $appPath . 'libs/',
            'locale'   => $appPath . 'locale/',
            'logs'     => $appPath . 'logs/',
            'modules'  => $appPath . 'modules/',
            'tmp'      => $appPath . 'tmp/',
            'public'   => $basePath . 'public/',
        ];

    }

    /**
     * Get a directory path
     *
     * @param $name
     *
     * @return string|bool
     */
    public function get($name) {

        if(!empty($this->dirs[$name])) {
            return $this->dirs[$name];
        } else {
            return false;
        }

    }

    /**
     * Set a directory path
     *
     * @param string $name
     * @param string $path
     */
    public function set($name, $path) {

        $this->dirs[$name] = $path;

    }

}