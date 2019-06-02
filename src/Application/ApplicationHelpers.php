<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Application {

    /**
     * Class ApplicationHelpers
     *
     * @package Rf\Core\Application
     */
    class ApplicationHelpers {

        /**
         * This function goal is to use the Autoloader to load the current file containing
         * useful helpers (functions)
         */
        public static function init() { }

    }

}

namespace {

    use Rf\Core\Application\ApplicationCli;
    use Rf\Core\Application\ApplicationMvc;
    use Rf\Core\Application\Components\Route;
    use Rf\Core\Application\Components\ServiceProvider;
    use Rf\Core\Base\ParameterSet;
    use Rf\Core\Http\Request;
    use Rf\Core\Utils\Format\Json;

    /**
     * Get the running app instance
     *
     * @return ApplicationCli|ApplicationMvc
     */
    function rf_app() {

        if(defined('APPLICATION_TYPE') && APPLICATION_TYPE == 'cli') {
            return ApplicationCli::getInstance();
        } else {
            return ApplicationMvc::getInstance();
        }

    }

    /**
     * Get service provider
     *
     * @return ServiceProvider
     */
    function rf_sp() {

        return rf_app()->getServiceProvider();

    }

    ////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////

    /**
     * Register an action
     *
     * @param string $hookName
     * @param mixed $action
     */
    function rf_add_action($hookName, $action) {

        rf_app()->registerAction($hookName, $action);

    }

    /**
     * Execute a actions for a specific hook
     *
     * @param string $hookName
     */
    function rf_exec_actions($hookName) {

        rf_app()->executeActions($hookName);

    }

    ////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////

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

    ////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////

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

    ////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////

    /**
     * Get and format the current date
     *
     * @param string $format Output date format
     *
     * @return string
     * @throws \Exception
     */
    function rf_date($format) {

        $date = new Rf\Core\Base\Date();

        return $date->format($format);

    }

    /**
     * This function convert a date string from a given format to another
     *
     * @param string $formatFrom
     * @param string $formatTo
     * @param string $date
     *
     * @return string
     * @throws \Exception
     */
    function rf_date_fromto($formatFrom, $formatTo, $date) {

        $date = new Rf\Core\Base\Date($date, $formatFrom);

        return $date->format($formatTo);

    }

    /////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////

    /**
     * Detect if the visitor is a known user agent (bot)
     *
     * @return bool
     */
    function is_bot() {

        return (preg_match('([bB]ot|[sS]pider|[yY]ahoo)', $_SERVER['HTTP_USER_AGENT'])) ? true : false;

    }

    /////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////

    /**
     * Sort an array of arrays using a common key present in the child arrays. The {$type}
     * argument allows to define the check type for the values.
     *
     * @param array $arrayOfArrays Array of array to sort
     * @param string $key Key name to use to sort elements
     * @param string $type date|number|other
     * @param string $order asc|desc
     *
     * @return array
     */
    function rf_aasort(array $arrayOfArrays, $key, $type, $order = 'asc') {

        if ($type == 'date') {

            $comp = function ($a, $b) use ($key) {
                $date1 = new Rf\Core\Base\Date($a[$key]);
                $date2 = new Rf\Core\Base\Date($b[$key]);
                if ($date1 == $date2) {
                    return 0;
                }
                return ($date1 < $date2) ? -1 : 1;
            };

        } elseif ($type == 'number') {

            $comp = function ($a, $b) use ($key) {
                if ($a[$key] == $b[$key]) {
                    return 0;
                }
                return ($a[$key] < $b[$key]) ? -1 : 1;
            };

        } else {

            $comp = function ($a, $b) use ($key) {
                return strnatcmp($a[$key], $b[$key]);
            };

        }

        usort($arrayOfArrays, $comp);
        if ($order == 'desc') {
            $arrayOfArrays = array_reverse($arrayOfArrays);
        }

        return $arrayOfArrays;

    }

    /**
     * This function return the value referenced in array with given key then
     * unset this line.
     *
     * @param array $array Target array
     * @param mixed $key Key to extract
     *
     * @return mixed|false return the value or false on
     */
    function rf_array_extract(&$array, $key) {

        if(!isset($array[$key])) {
            return false;
        }

        $extractVal = $array[$key];
        unset($array[$key]);

        return $extractVal;

    }

    /**
     * Get a value in an array recursively
     *
     * @param array $array
     * @param string $key E.g: arrayKey.subArrayKey[...]
     *
     * @return mixed
     */
    function rf_array_get(array $array, $key) {

        // Split the key parts
        $keyParts = explode('.', $key);

        foreach($keyParts as $keyIndex => $keyName) {

            // Break loop if one of the key does not exist
            if(!isset($array[$keyName])) {
                break;
            }

            // Update current array with sub-array
            $array = $array[$keyName];

            if($keyIndex + 1 < count($keyParts)) {

                // Continue iteration while key is not the last
                continue;

            } else {

                // Return value
                return $array;

            }

        }

        return null;

    }

    /////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////

    /**
     * Get a directory path by name
     *
     * @param string $name Directory name
     *
     * @return string
     */
    function rf_dir($name) {

        return rf_app()->getDir($name);

    }

    /**
     * Set a directory path by name
     *
     * @param string $name Directory name
     * @param string $path Directory path
     */
    function rf_add_dir($name, $path) {

        rf_app()->setDir($name, $path);

    }

    /**
     * Get a configuration param
     *
     * @param string $name Param name (section.section.param)
     *
     * @return ParameterSet|mixed
     */
    function rf_config($name) {

        return rf_app()->getConfiguration()->get($name);

    }

    function rf_getimagesize($file) {

        return @getimagesize($file);

    }

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

        if(rf_config('debug.active')) {
            rf_app()->addDebugVar($var);
        }

        if(rf_config('logging.active')) {

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

    /**
     * Format template params
     *
     * @param array $vars
     *
     * @return string
     */
    function rf_template_vars(array $vars) {

        return json_encode(array_values($vars));

    }

    /**
     * PHP empty function wrapper
     *
     * @param mixed $var
     *
     * @return bool
     */
    function rf_empty($var) {

        return empty($var);

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
