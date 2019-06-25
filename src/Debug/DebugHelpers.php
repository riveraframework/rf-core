<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Debug {

    /**
     * Class DebugHelpers
     *
     * @package Rf\Core\Debug
     */
    class DebugHelpers {

        /**
         * This function goal is to use the Autoloader to load the current file containing
         * useful helpers (functions)
         */
        public static function init() { }

    }

}

namespace {

    use Rf\Core\Utils\Format\Json;

    /**
     * Display debug vars
     */
    function rf_debug_display() {

        $debugVars = rf_app()->getDebugVars();

        foreach($debugVars as $debugVar) {
            var_dump($debugVar);
        }

    }

    /**
     * Add a var to the debug array if debug is activated
     *
     * @param mixed $var
     * @param string $logType
     */
    function rf_debug($var, $logType = 'debug') {

        if(rf_sp()->getDebug()->isEnabled()) {
            rf_app()->addDebugVar($var);
        }

        if(rf_sp()->getLog()->isEnabled()) {

            if(is_array($var) || is_object($var)) {
                try {
                    $var = Json::encode($var);
                } catch (\Exception $e) {
                    $var = 'Debug error: ' . $e->getMessage();
                }
            }

            rf_log($logType, $var);

        }

    }

}

