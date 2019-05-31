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
use Rf\Core\Application\Components\Router;
use Rf\Core\Application\Components\ServiceProvider;
use Rf\Core\Cache\CacheService;
use Rf\Core\Cache\Exceptions\CacheConfigurationException;
use Rf\Core\Exception\ConfigurationException;
use Rf\Core\Exception\ErrorMessageException;
use Rf\Core\Http\Request;
use Rf\Core\I18n\I18n;
use Rf\Core\Session\SessionService;
use Rf\Core\Session\Sessions\MemcachedHaSession;
use Rf\Core\Session\Sessions\PhpSession;
use Rf\Core\System\Performance\Benchmark;
use Rf\Core\Utils\Debug\ErrorHandler;

/**
 * Class Application
 *
 * @package Rf\Core\Application
 */
class ApplicationMvc extends Application {

    /**
     * @var string Application name
     */
    protected $name;

    /**
     * @var array Hooks for custom actions
     *
     * @TODO: extend the hook system
     */
    protected $actions = [
        'init' => []
    ];

    /**
     * @var string Path to the configuration file
     */
    protected $configurationFile;

    /**
     * @var Configuration Current Configuration object
     */
    protected $configuration;

    /**
     * @var ApplicationDirectories Current Directories object
     */
    protected $directories;

    /** @var ServiceProvider ServiceProvider instance */
    protected $serviceProvider;

    /** @var Request Current Request object */
    protected $request;

    /** @var CacheService Current cache service */
    protected $cacheService;

    /** @var SessionService $sessionManager */
    protected $sessionManager;

    /** @var Router Current Router object */
    protected $router;

    /** @var array Vars to debug */
    protected $debugVars = [];

    /**
     * Start the application init process
     *
     * @throws ConfigurationException
     * @throws CacheConfigurationException
     * @throws \Exception
     */
    public function init() {

        // Start Benchmark tool
        Benchmark::init();
        Benchmark::log('init start');

        // Register directories in current context
        $this->directories = new ApplicationDirectories();

        // Init helpers and app classes autoload
        Autoload::init();

        // Register the service provider
        // @TODO: Register other modules as services
        $this->serviceProvider = new ServiceProvider();

        // Register application configuration
        if(!empty($this->configurationFile)) {
            $this->configuration = new Configuration($this->configurationFile);
        } else {
            $this->configuration = new Configuration();
        }

        Benchmark::log('configuration loaded');

        // Load cache handler
        if(!rf_empty(rf_config('cache'))) {
            $this->cacheService = new CacheService(rf_config('cache')->toArray());
        } else {
            $this->cacheService = new CacheService([]);
        }

        // Execute registered actions (init)
        $this->executeActions('init');

        // Start session
        $this->handleSession();

        // Get request info
        $this->request = new Request();

        // Multi-lang support
        if($this->configuration->get('options.i18n')) {
            I18n::init();
        }

        // Init router module and verify bad requests (based on requested domain)
        $this->router = new Router();

        Benchmark::log('init end');

    }

    /**
     * Register a function|method to be executed at some points of the application execution using hooks
     *
     * @param string $timing Hook name
     * @param string $action Action name (function or static method)
     *
     * @return boolean
     */
    public function registerAction($timing, $action) {

        if(in_array($timing, array_keys($this->actions))) {

            $this->actions[$timing][] = $action;

            return true;

        } else {
            return false;
        }

    }

    /**
     * Get the actions registered for a specific hook
     *
     * @param string $timing Hook name
     *
     * @return array
     */
    public function getActions($timing) {

        return !empty($this->actions[$timing]) ? $this->actions[$timing] : [];

    }

    /**
     * Execute the functions|methods for a specific hook
     *
     * @param $hookName
     */
    public function executeActions($hookName) {


        foreach($this->getActions($hookName) as $action) {

            if(is_string($action) && preg_match('#::#', $action)) {

                $actionParts = explode('::', $action);

                $actionParts[0]::$actionParts[1]();

            } else {
                $action();
            }

        }

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
     * Get the current session manager object
     *
     * @return SessionService
     */
    public function getSessionManager() {

        return $this->sessionManager;

    }

    /**
     * Get the current router object
     *
     * @return Router
     */
    public function getRouter() {

        return $this->router;

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
     * Handle the session
     */
    public function handleSession() {

        $this->sessionManager = new SessionService();

        $sessionsConfig = !rf_empty(rf_config('sessions')) ? rf_config('sessions')->toArray() : false;

        if(empty($sessionsConfig)) {
            return;
        }

        foreach($sessionsConfig as $sessionConfig) {

            $sessionName = !rf_empty($sessionConfig['name'])
                ? $sessionConfig['name']
                : session_name();

            if($sessionConfig['type'] === 'default') {

                $this->sessionManager->add(new PhpSession($sessionName));

                Benchmark::log('session started');

            } elseif($sessionConfig['type'] === 'memcached-ha') {

                $options = [];
                if(!empty($sessionConfig['handler'])) {
                    $options['handler'] = rf_cache()->getHandler($sessionConfig['handler']);
                }
                if(!empty($sessionConfig['map'])) {
                    $options['map'] = $sessionConfig['map'];
                }
                if(!empty($sessionConfig['duration'])) {
                    $options['duration'] = $sessionConfig['duration'];
                }

                $this->sessionManager->add(new MemcachedHaSession($sessionName, $options));

            }

            if(!empty($sessionConfig['autostart'])) {
                $this->sessionManager->get($sessionName)->start();
            }

        }

    }

    /**
     * Start the application execution using the main controller property
     *
     * @TODO: Add user customizable error handler
     *
     * @throws \Exception
     */
    public function handleRequest() {

        Benchmark::log('handle request start');

        try {

            // Get the applicable route
            $this->router->route();
            $route = $this->router->getCurrentRoute();

            // Get the controller name
            $controllerName = $route['controller'];

            // Create the controller instance
            $controller = new $controllerName();

            // Get the action name
            $actionName = $route['action'];

            // Execute the requested action
            if(method_exists($controller, 'wrapper')) {

                Benchmark::log($controllerName . '::' . $actionName . ' started (with wrapper)');
                $controller->wrapper($actionName);

            } else {

                Benchmark::log($controllerName . '::' . $actionName . ' started');
                $controller->$actionName();

            }

        } catch(\Error $error) {

            if(
                (!rf_request()->isAjax() && rf_config('options.debug'))
                || (rf_request()->isAjax() && rf_config('options.debug-ajax'))
            ) {

                echo 'Execution time: ' . (microtime(true) - APPLICATION_START) . 's';
                rf_debug_display();

                echo ErrorHandler::formatError($error);

            } elseif(rf_request()->isApi()) {

                throw new ErrorMessageException($error->getMessage());

            } else {

                die($error->getMessage());

            }

        }

    }

    /**
     * Get the current request object
     *
     * @return Request
     * @throws \Exception
     */
    public function getRequest() {

        if(!isset($this->request)) {

            throw new \Exception('Undefined request');

        }

        return $this->request;

    }

    /**
     * Get the current cache service
     *
     * @return CacheService
     * @throws \Exception
     */
    public function getCacheService() {

        if(!isset($this->cacheService)) {

            throw new \Exception('Undefined cache service');

        }

        return $this->cacheService;

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