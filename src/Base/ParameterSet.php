<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Base;

/**
 * Class ParameterSet
 *
 * @package Rf\Core\Base
 */
class ParameterSet {

    /** @var array Array of variable stored in the ParameterSet */
    public $vars = [];

    /**
     * ParameterSet Constructor.
     *
     * @param array|object $params
     * @param bool $skipObjects
     */
    public function __construct($params, $skipObjects = true) {

        $this->buildSet($params, $skipObjects);

    }

    /**
     * Get a property
     *
     * @param string $key Property name
     *
     * @return ParameterSet|mixed
     */
    public function get($key) {

        if(!isset($this->vars[$key])) {
            return false;
        } else {
            return $this->vars[$key];
        }

    }

    /**
     * Set a property
     *
     * @param string $key Property name
     * @param mixed $value
     */
    public function set($key, $value) {

        $this->vars[$key] = $value;

    }

    /**
     * Unset a property
     *
     * @param string $key Property name
     */
    public function remove($key) {

        if(isset($this->vars[$key])) {
            unset($this->vars[$key]);
        }

    }

    /**
     * Check if a property exists
     *
     * @param string $key Property name
     *
     * @return bool
     */
    public function exists($key) {

        return isset($this->vars[$key]);

    }

    /**
     * Get the ParameterSet vars as an array
     *
     * @return array
     */
    public function toArray() {

        $vars = [];

        foreach ($this->vars as $key => $var) {

            /** @var ParameterSet|mixed $var */
            if(is_a($var, static::class)) {
                $vars[$key] = $var->toArray();
            } else {
                $vars[$key] = $var;
            }

        }

        return $vars;

    }

    /**
     * Get the vars count int the current ParameterSet instance
     *
     * @return int
     */
    public function count() {

        return count($this->vars);

    }

    /**
     * Build the set
     *
     * @param mixed $params
     * @param bool $skipObjects
     */
    protected function buildSet($params, $skipObjects) {

        if(!$skipObjects && is_object($params)) {

            foreach(get_object_vars($params) as $prop => $value) {

                if((is_array($value) || is_object($value)) && !empty($value)) {
                    $this->vars[$prop] = new static($value);
                } else {
                    $this->vars[$prop] = $value;
                }

            }

        } elseif(is_array($params)) {

            foreach($params as $prop => $value) {

                if((is_array($value) || is_object($value)) && !empty($value)) {
                    $this->vars[$prop] = new static($value);
                } else {
                    $this->vars[$prop] = $value;
                }

            }

        } else {
            $this->vars = $params;
        }

    }

}