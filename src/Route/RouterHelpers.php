<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Route {

    /**
     * Class RouterHelpers
     *
     * @package Rf\Core\Route
     */
    class RouterHelpers {

        /**
         * This function goal is to use the Autoloader to load the current file containing
         * useful helpers (functions)
         */
        public static function init() { }

    }

}

namespace {

    /**
     * Generate a link
     *
     * @param string $routeName,... Unlimited number of parameters depending on your route rules
     * @param array $args
     *
     * @return string
     */
    function rf_link_to($routeName, $args = []) {

        return rf_app()->getRouter()->link_to($routeName, $args);

    }

    /**
     * Generate a link to the current page in the target language
     *
     * @param string $language
     *
     * @return string
     */
    function rf_switch_language($language) {

        return rf_link_to(null, ['language' => $language]);

    }

    /**
     * Get the current url
     *
     * @return string
     */
    function rf_current_url() {

        return rf_config('app.url') . rf_switch_language(rf_current_language());

    }

    /**
     * Get the current route
     *
     * @return Route
     * @throws \Exception
     */
    function rf_current_route() {

        return rf_app()->getRouter()->getCurrentRoute();

    }

    /**
     * Check if the current route is the same as the one provided
     *
     * @param string $routeName
     *
     * @return bool
     * @throws \Exception
     */
    function rf_is_current_route($routeName) {

        $currentRoute = rf_current_route();
        return $currentRoute->getName() == $routeName . '_' . rf_current_language();

    }

}

