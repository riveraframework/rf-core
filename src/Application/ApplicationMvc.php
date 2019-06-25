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

use Rf\Core\Application\Interfaces\ApplicationInterface;
use Rf\Core\Config\DirectoriesSet;
use Rf\Core\Http\Request;
use Rf\Core\Service\ServiceProvider;
use Rf\Core\System\Performance\Benchmark;

/**
 * Class Application
 *
 * @package Rf\Core\Application
 */
class ApplicationMvc extends Application implements ApplicationInterface {

    /** @var string  */
    const TYPE = 'mvc';

    /** @var Request Current Request object */
    protected $request;

    /** @var ApplicationMvc */
    protected static $applicationInstance;

    /**
     * Start the application init process
     *
     * @throws \Exception
     */
    public function init() {

        // Define the application type
        define('APPLICATION_TYPE', self::TYPE);

        // Start Benchmark tool
        Benchmark::init();
        //Benchmark::log('init start');

        // Init helpers and app classes autoload
        Autoload::init();

        // Register the service provider
        $this->serviceProvider = new ServiceProvider();

        // Register directories in current context
        $this->directories = new DirectoriesSet();

        // Register application configuration
        $this->registerDefaultConfigService();

        //Benchmark::log('configuration loaded');

        // Load services
        $this->loadServices();

        Benchmark::log('services loaded');

        // Get request info
        $this->request = new Request();

        //Benchmark::log('request parsed');
        Benchmark::log('init end');

    }

    /**
     * Execute code before the request is handled
     *
     * @param callable $callback
     */
    public function before(callable $callback) {

        array_push($this->hooks['before_handle_request'], $callback);

    }

    /**
     * Execute code before the request is handled
     *
     * @param callable $callback
     */
    public function after(callable $callback) {

        array_push($this->hooks['after_handle_request'], $callback);

    }

    /**
     * Get the current request object
     *
     * @return Request
     */
    public function getRequest() {

        return $this->request;

    }

    /**
     * Handle the request
     *
     * @throws \Exception
     */
    public function handleRequest() {

        $this->serviceProvider->getRouter()->handleRequest($this->request);

    }

    /**
     * Get the current MVC application instance
     *
     * @return ApplicationMvc
     */
    final public static function getApp() {

        if (!isset(self::$applicationInstance)) {

            self::$applicationInstance = new self();

        }

        return self::$applicationInstance;

    }

}