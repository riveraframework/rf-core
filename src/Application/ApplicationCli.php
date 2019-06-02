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
use Rf\Core\Application\Exceptions\ConfigurationException;
use Rf\Core\Cache\CacheService;
use Rf\Core\Cache\Exceptions\CacheConfigurationException;
use Rf\Core\I18n\I18n;
use Rf\Core\System\Performance\Benchmark;

/**
 * Class ApplicationCli
 *
 * @package Rf\Core\Application
 */
class ApplicationCli extends Application {

    /** @var ApplicationCli */
    protected static $applicationInstance;
    
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
        
        // Multi-lang support
        if($this->configuration->get('options.i18n') == true) {
            I18n::init();
        }

        Benchmark::log('init end');

    }

    /**
     * Get the current cli application instance
     *
     * @return ApplicationCli
     */
    final public static function getInstance() {

        if (!isset(self::$applicationInstance)) {

            self::$applicationInstance = new self();

        }

        return self::$applicationInstance;

    }

}