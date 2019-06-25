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

use Rf\Core\Base\ParameterSet;

/**
 * Class ConfigurationSet
 *
 * @package Rf\Core\Config
 */
class ConfigurationSet extends ParameterSet {

    /**
     * ParameterSet Constructor.
     *
     * @param array|object $params
     * @param bool $skipObjects
     * @param ConfigurationSet|null $sharedConfigs
     */
    public function __construct($params, $skipObjects = true, $sharedConfigs = null) {

        $this->buildSet($params, $skipObjects, $sharedConfigs);

    }

    /**
     * Get a property in a section of the configuration
     * This supports recursive lookup, e.g: my_section.my_sub_section.my_var
     *
     * @param string $section Section name
     *
     * @return ParameterSet|mixed
     */
    public function get($section) {

        $sectionParts = explode('.', $section);

        /** @var ParameterSet|mixed $section */
        $section = $this->vars;

        $value = false;
        foreach($sectionParts as $sectionIndex => $sectionName) {

            if(is_array($section)) {

                if(!isset($section[$sectionName])) {
                    return $value;
                }

                $section = $section[$sectionName];
            } elseif(is_a($section, static::class)) {

                $section = $section->get($sectionName);

            } else {
                break;
            }

            if(
                $sectionIndex + 1 < count($sectionParts)
                && (
                    is_a($section, static::class)
                    || is_array($section)
                )
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
     * Build the set
     *
     * @param mixed $params
     * @param bool $skipObjects
     * @param ConfigurationSet|null $sharedConfigs
     */
    protected function buildSet($params, $skipObjects, $sharedConfigs = null) {

        if(!$skipObjects && is_object($params)) {

            foreach(get_object_vars($params) as $prop => $value) {

                if((is_array($value) || is_object($value)) && !empty($value)) {
                    $this->vars[$prop] = new static($value, $skipObjects, $sharedConfigs);
                } else {
                    $this->vars[$prop] = $value;
                }

            }

        } elseif(is_array($params)) {

            if(isset($params['shared_configs'])) {

                $sharedConfigs = new static($params['shared_configs']);

            }

            foreach($params as $prop => $value) {

                if($prop === 'shared_configs') {
                    continue;
                }

                if((is_array($value) || is_object($value)) && !empty($value)) {
                    $this->vars[$prop] = new static($value, $skipObjects, $sharedConfigs);
                } else {

                    if(is_string($value) && strpos($value, 'shared_configs') === 0) {
                        $name = str_replace('shared_configs:', '', $value);
                        $this->vars[$prop] = $sharedConfigs->get($name);
                    } else {
                        $this->vars[$prop] = $value;
                    }

                }

            }

        } else {

            if(is_string($params) && strpos($params, 'shared_configs') === 0) {
                $name = str_replace('shared_configs:', '', $params);
                $this->vars = $sharedConfigs->get($name);
            } else {
                $this->vars = $params;
            }

        }

    }

}