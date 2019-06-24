<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Log {

    /**
     * Class SessionHelpers
     *
     * @package Rf\Core\Log
     */
    class LogHelpers {

        /**
         * This function goal is to use the Autoloader to load the current file containing
         * useful helpers (functions)
         */
        public static function init() { }

    }

}

namespace {

	/**
	 * Log a message
     *
     * @param string $type
     * @param string $message
	 */
    function rf_log($type, $message) {

    	new Rf\Core\Log\LogService($type, $message);

    }

}

