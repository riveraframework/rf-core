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
 * Class Route
 * 
 * @package Rf\Core\Routing
 */
class Route {

    /** @var string $name Route name */
    protected $name;

    /** @var string $url Route uri pattern */
    protected $pattern;

    /** @var array Route methods */
    protected $methods;

    /** @var string Controller to instantiate */
    protected $controller;

    /** @var string Action (method) to execute */
    protected $action;

    /** @var array $defaults Default values for params */
    protected $defaults = [];

    /** @var string $domain Applicable domain(s) */
    protected $domains = [];

    /**
     * Route constructor.
     *
     * @param $name
     * @param array $routeParams
     *
     * @throws \Exception
     */
    public function __construct($name, array $routeParams) {

        $this->name = $name;

        if(empty($routeParams['pattern'])) {
            throw new \Exception('Route "' . $name . '" missing a pattern');
        } else {
            $this->pattern = $routeParams['pattern'];
        }

        if(empty($routeParams['methods'])) {
            throw new \Exception('Route "' . $name . '" missing a method');
        } else {

            if(!is_array($routeParams['methods'])) {
                $this->methods = [$routeParams['methods']];
            } else {
                $this->methods = $routeParams['methods'];
            }

        }

        if(!empty($routeParams['controller'])) {
            $this->controller = $routeParams['controller'];
        }

        if(!empty($routeParams['action'])) {
            $this->action = $routeParams['action'];
        }

        if(!empty($routeParams['defaults'])) {
            $this->defaults = $routeParams['defaults'];
        }

        if(!empty($routeParams['domains'])) {
            $this->domains = $routeParams['domains'];
        }

    }

    /**
     * Get the route name
     *
     * @return string
     */
    public function getName() {

        return $this->name;

    }

    /**
     * Get the route pattern
     *
     * @return string
     */
    public function getPattern() {

        return $this->pattern;

    }

    /**
     * Get the route methods
     *
     * @return array
     */
    public function getMethods() {

        return $this->methods;

    }

    /**
     * Get the controller
     *
     * @return string
     */
    public function getController() {

        return $this->controller;

    }

    /**
     * Get the action
     *
     * @return string
     */
    public function getAction() {

        return $this->action;

    }

    /**
     * Get the route parameter default values
     *
     * @return array
     */
    public function getDefaults() {

        return $this->defaults;

    }

    /**
     * Generate the regex corresponding to the pattern
     *
     * @return string
     */
    public function generateRegex() {

        // Replace params
        $regex = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $this->pattern);

        // Replace slashes
        $regex = str_replace('/', '\/', $regex);

        // Add delimiters and flags
        $regex = '/^' . $regex . '$/ui';

        return $regex;

    }

    /**
     * Check if the provided uri matches the route pattern
     * The uri needs to start by "/" (the domain portion is omitted)
     *
     * @param string $uri
     * @param array $foundParams
     *
     * @return bool
     */
    public function matchUri($uri, &$foundParams) {

        return (bool) preg_match($this->generateRegex(), $uri, $foundParams);

    }

    /**
     * Check if the provided method matches the route available methods
     *
     * @param string $method
     *
     * @return bool
     */
    public function matchMethod($method) {

        return in_array($method, $this->methods);

    }

}
