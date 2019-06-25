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

use Rf\Core\Cache\CacheService;
use Rf\Core\Config\ConfigService;
use Rf\Core\Config\DirectoriesSet;
use Rf\Core\Service\ServiceLauncherFactory;
use Rf\Core\Service\ServiceProvider;

/**
 * Class Application
 *
 * @package Rf\Core\Application
 */
abstract class Application {

    /** @var string Application name*/
    protected $name;

    /** @var string Path to the configuration file */
    protected $configurationFile;

    /** @var DirectoriesSet Current Directories object */
    protected $directories;

    /** @var ServiceProvider ServiceProvider instance */
    protected $serviceProvider;

    /** @var CacheService Current cache service */
    protected $cacheService;

    /** @var array Vars to debug */
    protected $debugVars = [];

    /**
     * @var array Hooks for custom actions
     *
     * @TODO: extend the hook system
     */
    protected $hooks = [
        'init' => [],
        'before_handle_request' => [],
        'after_handle_request' => [],
    ];

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
     * Register the default config service
     */
    public function registerDefaultConfigService() {

        // Define the config service configuration
        if(!empty($this->configurationFile)) {
            $configuration = [
                'file' => $this->configurationFile
            ];
        } else {
            $configuration = [
                'file' => rf_dir('config') . 'config.php'
            ];
        }

        // Create a new config service launcher
        $launcher = ServiceLauncherFactory::createConfigServiceLauncher(ConfigService::TYPE, 'config', $configuration, true);

        // Register the service
        $this->serviceProvider->add(ConfigService::TYPE, 'config', $launcher, true);

    }

    /**
     * Load the services from the config file
     *
     * @throws Exception
     */
    public function loadServices() {

        // Get the services from the configuration
        $services = $this->serviceProvider->getConfig()->getServices();

        foreach ($services as $service) {

            // Define definition args
            $type = isset($service['definition']['type']) ? $service['definition']['type'] : '';
            $name = isset($service['definition']['name']) ? $service['definition']['name'] : '';
            $enabled = isset($service['definition']['enabled']) && !$service['definition']['enabled'] ? false : true;
            $default = isset($service['definition']['default']) && $service['definition']['default'] ? true : false;
            $configuration = !empty($service['configuration']) ? $service['configuration'] : [];

            // Skip disabled services
            if(!$enabled) {
                continue;
            }

            switch ($type) {

                case 'cache':

                    // Create a new cache service launcher
                    $launcher = ServiceLauncherFactory::createCacheServiceLauncher($type, $name, $configuration, $default);

                    break;

                case 'debug':

                    // Create a new debug service launcher
                    $launcher = ServiceLauncherFactory::createDebugServiceLauncher($type, $name, $configuration, $default);

                    break;

                case 'i18n':

                    // Create a new i18n service launcher
                    $launcher = ServiceLauncherFactory::createI18nServiceLauncher($type, $name, $configuration, $default);

                    break;

                case 'log':

                    // Create a new log service launcher
                    $launcher = ServiceLauncherFactory::createLogServiceLauncher($type, $name, $configuration, $default);

                    break;

                case 'orm':

                    // Create a new orm service launcher
                    $launcher = ServiceLauncherFactory::createOrmServiceLauncher($type, $name, $configuration, $default);

                    break;

                case 'router':

                    // Create a new router service launcher
                    $launcher = ServiceLauncherFactory::createRouterServiceLauncher($type, $name, $configuration, $default);

                    break;

                case 'session':

                    // Create a new session service launcher
                    $launcher = ServiceLauncherFactory::createSessionServiceLauncher($type, $name, $configuration, $default);

                    break;

                default:

                    if(class_exists($type)) {

                        // In this case the type is supposed to be a class name so we need to get the real type using
                        // the value of the class constant TYPE. If the constant in not defined we use the class name
                        // as service type.
                        if(defined($type . '::TYPE')) {
                            $realType = $type::TYPE;
                        } else {
                            $realType = $type;
                        }

                        // Create a new custom service launcher
                        $launcher = ServiceLauncherFactory::createCustomServiceLauncher($type, $realType, $name, $configuration, $default);

                    }

                    break;

            }

            // Register the service launcher
            if(isset($launcher)) {

                $this->serviceProvider->add($type, $name, $launcher, $default);

            }

        }

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

    /**
     * Register a function|method to be executed at some points of the application execution using hooks
     *
     * @param string $timing Hook name
     * @param string $action Action name (function or static method)
     *
     * @return boolean
     */
    public function registerAction($timing, $action) {

        if(in_array($timing, array_keys($this->hooks))) {

            $this->hooks[$timing][] = $action;

            return true;

        } else {
            return false;
        }

    }

    /**
     * Get the actions registered for a specific hook
     *
     * @param string $hookName Hook name
     *
     * @return array
     */
    public function getHooks($hookName) {

        return !empty($this->hooks[$hookName]) ? $this->hooks[$hookName] : [];

    }

    /**
     * Execute the functions|methods for a specific hook
     *
     * @param $hookName
     */
    public function executeActions($hookName) {

        foreach($this->getHooks($hookName) as $action) {

            if(is_string($action) && preg_match('#::#', $action)) {

                $actionParts = explode('::', $action);

                $actionParts[0]::$actionParts[1]();

            } else {
                $action();
            }

        }

    }

}