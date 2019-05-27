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

use Rf\Core\Base\ParameterSet;
use Rf\Core\Exception\ConfigurationException;

/**
 * Class Configuration
 *
 * @package Rf\Core\Configuration
 */
class ApplicationConfiguration {

    /** @var ParameterSet Configuration params */
    protected $configuration;

    /** @var string Configuration file path */
    protected $configurationFile;

    /**
     * Initiate a new configuration object by loading the configuration file into some
     * ParameterSet in the Configuration object
     *
     * @param string $configurationFile Path to configuration file
     *
     * @throws ConfigurationException
     */
    public function __construct($configurationFile = null) {

        if(!empty($configurationFile)) {
	        $this->load($configurationFile);
        } else {
	        $this->load(rf_dir('config') .'/config.' . APPLICATION_ENV . '.php');
        }

    }

    /**
     * Get a property in a section of the configuration
     *
     * @param string $section Section name
     *
     * @return ApplicationConfigurationParameterSet|mixed
     */
    public function get($section) {

    	$sectionParts = explode('.', $section);

	    /** @var ParameterSet|mixed $section */
	    $section = $this->configuration;

	    // @TODO Replace false by -1?
	    $value = false;
	    foreach($sectionParts as $sectionIndex => $sectionName) {

		    $section = $section->get($sectionName);

		    if(
		        $sectionIndex + 1 < count($sectionParts)
                && is_a($section, ApplicationConfigurationParameterSet::class)
            ) {
		    	continue;
		    } else {
		    	$value = $section;
		    	break;
		    }

	    }

        return $value;

    }

    /**
     * Load the configuration file and map the variables in the Configuration object
     *
     * @param string $configurationFile
     *
     * @throws ConfigurationException
     */
    protected function load($configurationFile) {

    	if(!file_exists($configurationFile)) {
		    throw new ConfigurationException('Configuration', 'The configuration file does not exist');
	    }

        // Get configuration file content
        $cfg = include $configurationFile;

        // If the configuration cannot be load it raise a ConfigurationException
        if (empty($cfg)) {
            throw new ConfigurationException('Configuration', 'The configuration file is empty');
        }

        // Else we map the data in ParameterSet
        $this->configuration = new ApplicationConfigurationParameterSet($cfg);

    }
    
    //@TODO: Add static method avec alias
}