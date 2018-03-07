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

use Rf\Core\Api\Api;
use Rf\Core\Authentication\Authentication;
use Rf\Core\Base\ErrorHandler;
use Rf\Core\Cache\CacheService;
use Rf\Core\Cache\Exceptions\CacheConfigurationException;
use Rf\Core\Entity\Architect;
use Rf\Core\Exception\BaseException;
use Rf\Core\Exception\ConfigurationException;
use Rf\Core\Exception\ErrorMessageException;
use Rf\Core\Http\Request;
use Rf\Core\I18n\I18n;
use Rf\Core\Routing\Router;
use Rf\Core\Uri\Uri;

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
     * @var ApplicationConfiguration Current Configuration object
     */
    protected $configuration;
    
    /**
     * @var ApplicationDirectories Current Directories object
     */
    protected $directories;

    /** @var ServiceProvider ServiceProvider intance */
    protected $serviceProvider;
    
    /**
     * @var Architect Current architect object
     *
     * @TODO: Don't put the architect in the app by default
     */
    protected $architect;

    /** @var Request Current Request object */
    protected $request;

    /** @var CacheService Current cache service */
    protected $cacheService;
    
    /** @var Router Current Router object */
    protected $router;

    /** @var array Vars to debug */
    protected $debugVars = [];
    
    /**
     * Start the application init process
     *
     * @throws ConfigurationException
     * @throws CacheConfigurationException
     */
    public function init() {

        // Register directories in current context
        $this->directories = new ApplicationDirectories();

        // Init helpers and app classes autoload
        Autoload::init();

        // Register the service provider
        $this->serviceProvider = new ServiceProvider();
        
        // Register application configuration
        if(!empty($this->configurationFile)) {
            $configuration = new ApplicationConfiguration($this->configurationFile);
        } else {
            $configuration = new ApplicationConfiguration();
        }
        $this->configuration = $configuration;

        // Load cache handler
	    if(!rf_empty(rf_config('cache'))) {
	    	$this->cacheService = new CacheService(rf_config('cache')->toArray());
	    }
        
        // Execute registered actions (init)
        $this->executeActions('init');
        
        // Start session
        ini_set('session.cookie_domain', '.' . Uri::getDomainFromUri($this->configuration->get('app.url')));
        if(!rf_empty(rf_config('session.cookie_lifetime'))) {
	        ini_set('session.cookie_lifetime', rf_config('session.cookie_lifetime'));
        }
        if(!rf_empty(rf_config('session.gc_maxlifetime'))) {
	        ini_set('session.gc_maxlifetime', rf_config('session.gc_maxlifetime'));
        }
        session_start();
        
        // Get request info
        $this->request = new Request();
        if($this->request->isApiFollow()) {
            Api::handleRequest();
        }

        // Init auth
	    Authentication::init();
        
        // Load Architect
        $this->architect = new Architect();
        
        // Multi-lang support
        if($this->configuration->get('options.i18n') == true) {

            try {
                I18n::init();
            } catch(BaseException $e) {}

        }
        
        // Init router module and verify bad requests (based on requested domain)
        $this->router = new Router();
        $this->router->testDomain();

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
     * Get the current Architect object
     *
     * @return Architect
     */
    public function architect() {

        return $this->architect;

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
     * Start the application execution using the main controller property
     */
    public function handleRequest() {

    	try {

		    $this->router->route();

		    $route = $this->router->getCurrentRoute();

		    $controllerName = $route['controller'];
		    $actionName = $route['action'];

		    $controller = new $controllerName();
		    $controller->$actionName();

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
     */
    public function getRequest() {

        if(!isset($this->request)) {
            // throw exception
        }

        return $this->request;

    }

    /**
     * Get the current cache service
     *
     * @return CacheService
     */
    public function getCacheService() {

        if(!isset($this->cacheService)) {
            // @TODO: Throw exception
	        return null;
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
     * @return ApplicationConfiguration
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