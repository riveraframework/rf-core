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
 * @version 1.0
 * @since 1.0
 */
class ParameterSet {
    
    /**
     * Array of variable stored in the ParameterSet
     * @var array
     */
    public $vars = [];

    /**
     * Constructor
     * 
     * @param mixed $params 
     */
    public function __construct($params) {

        if(is_object($params)) {

            foreach(get_object_vars($params) as $prop => $value) {

                if((is_array($value) || is_object($value)) && !empty($value)) {
                    $this->vars[$prop] = new ParameterSet($value);
                } else {
                    $this->vars[$prop] = $value;
                }

            }

        } elseif(is_array($params)) {

            foreach($params as $prop => $value) {

                if((is_array($value) || is_object($value)) && !empty($value)) {
                    $this->vars[$prop] = new ParameterSet($value);
                } else {
                    $this->vars[$prop] = $value;
                }

            }

        } else {
            $this->vars = $params;
        }

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
	    	if(is_a($var, self::class)) {
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

}