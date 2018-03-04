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
use Rf\Core\Autoload;
use Rf\Core\Base\ErrorHandler;
use Rf\Core\Base\GlobalSingleton;
use Rf\Core\Application\ApplicationConfiguration;
use Rf\Core\Entity\Architect;
use Rf\Core\Exception\BaseException;
use Rf\Core\Http\Request;
use Rf\Core\I18n\I18n;
use Rf\Core\Routing\Router;
use Rf\Core\Uri\Uri;

/**
 * Class ApplicationCron
 *
 * @package Rf\Core\Application
 */
class ApplicationCron extends Application {
    
    /**
     * @var string Application name
     */
    protected $name;
    
    /**
     * @var string Path to the configuration file
     */
    protected $configurationFile;
    
    /**
     * @var ApplicationConfiguration Current Configuration object
     */
    protected $configuration;
    
    /**
     *
     * @var ApplicationDirectories Current Directories object
     */
    protected $directories;

    /** @var ServiceProvider ServiceProvider intance */
    protected $serviceProvider;
    
    /**
     * @var Architect Current architect object
     *
     * @TODO: Keep Architect as a normal class?
     */
    protected $architect;

    /** @var array Vars to debug */
    protected $debugVars;
    
    
    /**
     * Start the application init process
     *
     * @param Autoload $autoload Directories object to set
     */
    public function init($autoload) {

        // Register the service provider
        $this->serviceProvider = new ServiceProvider();
        
        // Register directories in current context
        $this->directories = $autoload->getDirectories();

	    // Load Rf helpers
	    ApplicationHelpers::init();
        
        // Register application configuration
        if(!empty($this->configurationFile)) {
            $configuration = new ApplicationConfiguration($this->configurationFile);
        } else {
            $configuration = new ApplicationConfiguration();
        }
        $this->configuration = $configuration;
        
        // Load Architect
        $this->architect = new Architect();
        
        // Multi-lang support
        if($this->configuration->get('options.i18n') == true) {

            try {
                I18n::init();
            } catch(BaseException $e) {}

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