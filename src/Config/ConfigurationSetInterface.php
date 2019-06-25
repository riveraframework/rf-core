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
interface ConfigurationSetInterface {

    /**
     * Get a property in a section of the configuration
     * This supports recursive lookup, e.g: my_section.my_sub_section.my_var
     *
     * @param string $section Section name
     *
     * @return ParameterSet|mixed
     */
    public function get($section);

}