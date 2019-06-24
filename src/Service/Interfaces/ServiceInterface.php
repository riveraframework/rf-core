<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Service\Interfaces;

/**
 * Interface ServiceInterface
 *
 * @package Rf\Core\Service\Interfaces
 */
interface ServiceInterface {

    /**
     * Service constructor.
     *
     * @param string $type
     * @param string $name
     * @param array $configuration
     * @param bool $default
     */
    public function __construct($type, $name, array $configuration, $default = false);

    /**
     * Get the service type
     *
     * @return string
     */
    public function getType();

    /**
     * Get the service name
     *
     * @return string
     */
    public function getName();

    /**
     * Check whether or not the current service is enabled
     *
     * @return bool
     */
    public function isEnabled();

    /**
     * Check whether or not the current service should be used as default
     *
     * @return bool
     */
    public function isDefault();

    /**
     * Load the configuration
     *
     * @param array $configuration
     */
    public function loadConfiguration(array $configuration);

    /**
     * Get the service configuration
     *
     * @return ConfigurationSet
     */
    public function getConfiguration();

}