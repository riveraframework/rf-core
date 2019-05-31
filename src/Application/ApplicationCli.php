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
use Rf\Core\Cache\Exceptions\CacheConfigurationException;
use Rf\Core\Entity\Architect;
use Rf\Core\Exception\ConfigurationException;
use Rf\Core\I18n\I18n;
use Rf\Core\System\Performance\Benchmark;

/**
 * Class ApplicationCli
 *
 * @package Rf\Core\Application
 */
class ApplicationCli extends Application {
    
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
    
    /**
     * @var Architect Current architect object
     *
     * @TODO: Keep Architect as a normal class?
     */
    protected $architect;

    /** @var CacheService Current cache service */
    protected $cacheService;

    /** @var array Vars to debug */
    protected $debugVars;
    
    
    /**
     * Start the application init process
     *
     * @throws ConfigurationException
     * @throws CacheConfigurationException
     */
    public function init() {

        // Start Benchmark tool
        Benchmark::init();
        Benchmark::log('init start');

        // Register directories in current context
        $this->directories = new Directories();

        // Init helpers and app classes autoload
        Autoload::init();

        // Register the service provider
        $this->serviceProvider = new ServiceProvider();
        
        // Register application configuration
        if(!empty($this->configurationFile)) {
            $configuration = new Configuration($this->configurationFile);
        } else {
            $configuration = new Configuration();
        }
        $this->configuration = $configuration;

        Benchmark::log('configuration loaded');

        // Load cache handler
        if(!rf_empty(rf_config('cache'))) {
            $this->cacheService = new CacheService(rf_config('cache')->toArray());
        }
        
        // Load Architect
        $this->architect = new Architect();
        
        // Multi-lang support
        if($this->configuration->get('options.i18n') == true) {
            I18n::init();
        }

        Benchmark::log('init end');

    }

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
     * Get the current service provider
     *
     * @return ServiceProvider
     */
    public function getServiceProvider() {

        return $this->serviceProvider;

    }

    /**
     * Get the current Architect object
     *
     * @return Architect
     */
    public function architect() {

        return $this->architect;

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
     * Get a configuration parameter by name
     *
     * @return Configuration
     */
    public function getConfiguration() {

        return $this->configuration;

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