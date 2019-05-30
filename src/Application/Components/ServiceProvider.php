<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Application\Components;

/**
 * Class ServiceProvider
 *
 * @package Rf\Core\Application\Components
 */
class ServiceProvider {

    /** @var array */
    protected $services = [];

    /**
     * Register a service
     *
     * @param string $name
     * @param mixed $service
     */
    public function registerService($name, $service) {

        $this->services[$name] = $service;

    }

    /**
     * Get a service
     *
     * @param string $name
     *
     * @return bool|mixed
     */
    public function getService($name) {

        if(isset($this->services[$name])) {
            return $this->services[$name];
        } else {
            return false;
        }
        
    }

}