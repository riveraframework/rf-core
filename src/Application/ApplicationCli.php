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

use \Exception;

use Rf\Core\Application\Interfaces\ApplicationInterface;
use Rf\Core\Config\DirectoriesSet;
use Rf\Core\Service\ServiceProvider;
use Rf\Core\System\Performance\Benchmark;

/**
 * Class ApplicationCli
 *
 * @package Rf\Core\Application
 */
class ApplicationCli extends Application implements ApplicationInterface {

    /** @var string  */
    const TYPE = 'cli';

    /** @var ApplicationCli */
    protected static $applicationInstance;
    
    /**
     * Start the application init process
     *
     * @throws Exception
     */
    public function init() {

        // Define the application type
        define('APPLICATION_TYPE', self::TYPE);

        // Start Benchmark tool
        Benchmark::init();
        Benchmark::log('init start');

        // Init helpers and app classes autoload
        Autoload::init();

        // Register the service provider
        $this->serviceProvider = new ServiceProvider();

        // Register directories in current context
        $this->directories = new DirectoriesSet();

        // Register application configuration
        $this->registerDefaultConfigService();

        Benchmark::log('configuration loaded');

        // Load services
        $this->loadServices();

        Benchmark::log('services loaded');
        Benchmark::log('init end');

    }

    /**
     * Get the current cli application instance
     *
     * @return ApplicationCli
     */
    final public static function getApp() {

        if (!isset(self::$applicationInstance)) {

            self::$applicationInstance = new self();

        }

        return self::$applicationInstance;

    }

}