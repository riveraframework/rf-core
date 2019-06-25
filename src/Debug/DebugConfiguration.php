<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Debug;

use Rf\Core\Config\ConfigurationSet;

/**
 * Class DebugConfiguration
 *
 * @package Rf\Core\Debug
 */
class DebugConfiguration extends ConfigurationSet {

    /**
     * Returns whether if the debug data needs to be displayed
     *
     * @return bool
     */
    public function isDisplayEnabled() {

        return $this->get('options.display');

    }

    /**
     * Returns whether the debug is enabled during ajax requests
     *
     * @return bool
     */
    public function isAjaxDebugEnabled() {

        return $this->get('options.ajax');

    }

    /**
     * Returns whether the benchmarking tool is enabled
     *
     * @return int
     */
    public function isBenchmarkEnabled() {

        return $this->get('options.benchmark');

    }

    /**
     * Get the default configuration value
     *
     * @return array
     */
    protected function getDefaultValues() {

        return [
            'options' => [
                'display' => false,
                'ajax' => false,
                'benchmark' => false,
            ]
        ];

    }

}
