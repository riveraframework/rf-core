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

/**
 * Class ParameterSet
 *
 * @package Rf\Core\Configuration
 */
class ApplicationConfigurationParameterSet {

	/**
     * Array of variable stored in the ParameterSet
     * @var array
     */
	public $vars = [];

	/**
	 * Constructor
	 *
	 * @param array $params
	 */
	public function __construct(array $params) {

		foreach ($params as $key => $value) {

			if(is_array($value)) {
				$this->vars[$key] = new ApplicationConfigurationParameterSet($value);
			} else {
				$this->vars[$key] = $value;
			}

		}

	}

	/**
	 * Get a property
	 *
	 * @param string $key Property name
	 *
	 * @return mixed
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
	 *
	 * @return void
	 */
	public function set($key, $value) {

		$this->vars[$key] = $value;

	}

    /**
     * Get the ParameterSet vars as an array
     *
     * @return array
     */
    public function toArray() {

        $vars = [];

        foreach ($this->vars as $key => $var) {
            /** @var ApplicationConfigurationParameterSet|mixed $var */
            if(is_a($var, self::class)) {
                $vars[$key] = $var->toArray();
            } else {
                $vars[$key] = $var;
            }
        }

        return $vars;

    }

}