<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Session {

    /**
     * Class SessionHelpers
     *
     * @package Rf\Core\Session
     */
    class SessionHelpers {

        /**
         * This function goal is to use the Autoloader to load the current file containing
         * useful helpers (functions)
         */
        public static function init() { }

    }

}

namespace {

	use Rf\Core\Application\Application;
	use Rf\Core\Session\SessionService;

	/**
	 * Get session service
	 *
	 * @return SessionService
	 */
    function rf_session() {

    	$sessionManager = rf_app()->getSessionManager();
    	if(empty($sessionManager)) {
    		return null;
	    } else {
    		return $sessionManager;
	    }

    }

}

