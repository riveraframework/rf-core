<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Http {

    /**
     * Class HttpHelpers
     *
     * @package Rf\Core\Http
     */
    class HttpHelpers {

        /**
         * This function goal is to use the Autoloader to load the current file containing
         * useful helpers (functions)
         */
        public static function init() { }

    }

}

namespace {

    use Rf\Core\Base\ParameterSet;
    use Rf\Core\Http\Request;

    /**
     * Return the current HTTP request
     *
     * @return Request
     */
    function rf_request() {

        return rf_app()->getRequest();

    }

    /**
     * Return the current HTTP query
     *
     * @return ParameterSet
     */
    function rf_request_query() {

        return rf_app()->getRequest()->get('query');

    }

    // Fix for PHP-FPM and NGINX where the function getallheaders does not exist
    // @link: http://php.net/manual/en/function.getallheaders.php#84262
    if (!function_exists('getallheaders')) {

        function getallheaders() {

            $headers = [];
            foreach ($_SERVER as $name => $value) {

                if (substr($name, 0, 5) == 'HTTP_') {
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                } else if ($name == 'CONTENT_TYPE') {
                    $headers['Content-Type'] = $value;
                } else if ($name == 'CONTENT_LENGTH') {
                    $headers['Content-Length'] = $value;
                }

            }

            return $headers;

        }

    }

}
