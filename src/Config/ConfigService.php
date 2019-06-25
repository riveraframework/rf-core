<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Config;

use Rf\Core\Config\Exceptions\ConfigException;
use Rf\Core\Log\LogService;
use Rf\Core\Service\Service;

/**
 * Class Configuration
 *
 * @package Rf\Core\Config
 */
class ConfigService extends Service {

    /** @var string  */
    const TYPE = 'config';

    /** @var ConfigurationSet Configuration params */
    protected $configuration;

    /** @var string Configuration file path */
    protected $configurationFile;

    /**
     * {@inheritDoc}
     *
     * @throws ConfigException
     */
    public function loadConfiguration(array $configuration) {

        if(!empty($configuration['file'])) {
            $configurationFile = $configuration['file'];
        } else {
            $configurationFile = rf_dir('config') .'/config.php';
        }

        // Check if the configuration file exists
        if(!file_exists($configurationFile)) {
            throw new ConfigException(LogService::TYPE_ERROR, 'The configuration file does not exist');
        }

        // Get configuration file content
        $cfg = include $configurationFile;

        // If the configuration cannot be loaded it raise a ConfigException
        if (empty($cfg)) {
            throw new ConfigException(LogService::TYPE_ERROR, 'The configuration file is empty');
        }

        // Else we map the data in ParameterSet
        $this->configuration = new ConfigurationSet($cfg);

    }

    /**
     * Get a value by key
     *
     * @return array
     */
    public function get($key) {

        return $this->configuration->get($key);

    }

    /**
     * Get the service included in the configuration
     *
     * @return array
     */
    public function getServices() {

        return $this->configuration->get('services')->toArray();

    }

    /**
     * Get the service included in the configuration
     *
     * @param string|null $name
     *
     * @return array
     * @throws ConfigException
     */
    public function getSharedConfigs($name = null) {

        if(isset($name)) {

            $sharedConfig = $this->configuration->get('shared_configs.' . $name);

            if(!$sharedConfig) {
                throw new ConfigException(LogService::TYPE_ERROR, 'The shared config "' . $name . '" is not defined');
            }

            return $sharedConfig->toArray();

        } else {

            return $this->configuration->get('shared_configs')->toArray();

        }

    }

}