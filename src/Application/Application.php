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

use Rf\Core\Application\Components\Configuration;
use Rf\Core\Application\Components\Directories;
use Rf\Core\Application\Components\ServiceProvider;
use Rf\Core\Cache\CacheService;

/**
 * Class Application
 *
 * @package Rf\Core\Application
 */
abstract class Application implements ApplicationInterface {

    /** @var string Application name*/
    protected $name;

    /** @var string Path to the configuration file */
    protected $configurationFile;

    /** @var Configuration Current Configuration object */
    protected $configuration;

    /** @var Directories Current Directories object */
    protected $directories;

    /** @var ServiceProvider ServiceProvider instance */
    protected $serviceProvider;

    /** @var CacheService Current cache service */
    protected $cacheService;

    /** @var array Vars to debug */
    protected $debugVars;

    /**
     * Get application name
     *
     * @return string
     */
    public function getName() {

        return $this->name;

    }

    /**
     * Set the configuration file path
     *
     * @param string $path Configuration file path
     */
    public function setConfigurationFile($path) {

        $this->configurationFile = $path;

    }

    /**
     * Get a configuration parameter by name
     *
     * @return Configuration
     */
    public function getConfiguration() {

        return $this->configuration;

    }

    /**
     * Get a directory by name
     *
     * @param string $name Directory name
     *
     * @return string
     */
    public function getDir($name) {

        return $this->directories->get($name);

    }

    /**
     * Override or add a new directory to the current list
     *
     * @param string $name Directory name
     * @param string $path Directory path
     */
    public function setDir($name, $path) {

        $this->directories->set($name, $path);

    }

    /**
     * Get the current service provider
     *
     * @return ServiceProvider
     */
    public function getServiceProvider() {

        return $this->serviceProvider;

    }

    /**
     * Add a var to the debugVars array
     *
     * @param mixed $var
     */
    public function addDebugVar($var) {

        $this->debugVars[] = $var;

    }

    /**
     * Get debug vars
     *
     * @return array
     */
    public function getDebugVars() {

        return $this->debugVars;

    }
    
}