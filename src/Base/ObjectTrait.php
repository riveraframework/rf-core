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
 * Class ObjectTrait
 *
 * @package Rf\Core\Base
 */
trait ObjectTrait {

    /**
     * Get a property
     *
     * @param string $param
     * @param null|string $subParam
     *
     * @return mixed
     */
    public function get($param, $subParam = null) {

        if(property_exists($this, $param) && !empty($this->{$param})) {

            if(!empty($subParam)) {

                if(!method_exists($this->{$param}, 'get')) {
                    return false;
                } else {
                    return $this->{$param}->get($subParam);
                }

            } else {
                return $this->{$param};
            }

        } else {
            return false;
        }

    }

    /**
     * Set a property
     *
     * @param string $param
     * @param mixed $value
     * @param null|string $subParam
     */
    public function set($param, $value, $subParam = null) {

        if(empty($subParam)) {
            $this->{$param} = $value;
        } else {
            $this->{$param}->set($subParam, $value);
        }

    }
    
}