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

	use Rf\Core\Application\Application;
	use Rf\Core\Application\ApplicationConfigurationParameterSet;
	use Rf\Core\Application\ApplicationCron;
    use Rf\Core\Application\ServiceProvider;
    use Rf\Core\Data\Generation\Random;
    use Rf\Core\Html\Breadcrumbs;
    use Rf\Core\Http\QueryParameterSet;
    use Rf\Core\Http\Request;
    use Rf\Core\I18n\I18n;

    /**
     * Get service provider
     *
     * @return ServiceProvider
     */
    function rf_sp() {

        return Application::getInstance()->getServiceProvider();

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

        Application::getInstance()->registerAction($hookName, $action);

    }

	/**
     * Execute a actions for a specific hook
     *
     * @param string $hookName
     */
    function rf_exec_actions($hookName) {

        Application::getInstance()->executeActions($hookName);

    }

    ////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////

    /**
     * Return the current HTTP request
     *
     * @return Request
     */
    function rf_request() {

        return Application::getInstance()->getRequest();

    }

	/**
     * Return the current HTTP query
     *
     * @return QueryParameterSet
     */
    function rf_request_query() {

        return Application::getInstance()->getRequest()->get('query');

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

        return Application::getInstance()->getRouter()->link_to($routeName, $args);

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

    function rf_current_url() {

        return rf_config('app.url') . rf_switch_language(rf_current_language());

    }

    function rf_current_route() {

        return Application::getInstance()->getRouter()->getCurrentRoute();

    }

    function rf_is_current_route($routeName) {

        $currentRoute = rf_current_route();
        if(strpos('api_', $routeName)) {
            return $currentRoute['name'] == $routeName;
        } else {
            return $currentRoute['name'] == $routeName . '_' . rf_current_language();
        }

    }

    ////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////

    /**
     * Get and format the current date
     *
     * @param string $format Output date format
     *
     * @return string
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
     */
    function rf_date_fromto($formatFrom, $formatTo, $date) {

        $date = new Rf\Core\Base\Date($date, $formatFrom);

        return $date->format($formatTo);

    }

    /////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////

    /**
     * Get the current language
     *
     * @return string
     */
    function rf_current_language() {

        return Rf\Core\I18n\I18n::$currentLanguage;

    }

    /**
     * Get the available languages
     *
     * @return array
     */
    function rf_available_languages() {

        return Rf\Core\I18n\I18n::$availableLanguages;

    }
    
    /**
     * Get the translation of a string
     *
     * @param string $msgid,...
     *
     * @return string
     */
    function __($msgid) {

        $args = func_get_args();
        array_shift($args);

        return I18n::translate($msgid, $args);

    }

    /**
     * Get the translation of a string from a data set
     *
     * @param string $key
     * @param array $dataset
     *
     * @return string
     */
    function _t($key, $dataset) {

	    $args = func_get_args();
	    array_shift($args);
	    array_shift($args);

        return I18n::translateFromDataSet($key, $dataset, $args);

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
    ///

    /**
     * Return a random float number between a min and a max
     *
     * @param float|int $min
     * @param float|int $max
     *
     * @return float|int
     */
    function rf_rand_float($min = 0, $max = 1) {

        return Random::float($min, $max);

    }

    /////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////

    /**
     * Get the current breadcrumbs elements
     *
     * @return array
     */
    function rf_breadcrumbs_elements() {

        return Breadcrumbs::getInstance()->getElements();

    }

    /**
     * Add an element to the breadcrumbs
     *
     * @param string $type link|list
     * @param string|array $value
     */
    function rf_breadcrumbs_add($type, $value) {

        Breadcrumbs::getInstance()->addElement($type, $value);

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

    	if(defined('APPLICATION_TYPE') && APPLICATION_TYPE == 'cron') {
		    return ApplicationCron::getInstance()->getDir($name);
	    } else {
		    return Application::getInstance()->getDir($name);
	    }

    }

    /**
     * Set a directory path by name
     *
     * @param string $name Directory name
     * @param string $path Directory path
     */
    function rf_add_dir($name, $path) {

	    if(defined('APPLICATION_TYPE') && APPLICATION_TYPE == 'cron') {
		    ApplicationCron::getInstance()->setDir( $name, $path );
	    } else {
		    Application::getInstance()->setDir( $name, $path );
	    }

    }

    /**
     * Get a configuration param
     *
     * @param string $name Param name (section.section.param)
     *
     * @return ApplicationConfigurationParameterSet|mixed
     */
    function rf_config($name) {

	    if(defined('APPLICATION_TYPE') && APPLICATION_TYPE == 'cron') {
		    return ApplicationCron::getInstance()->getConfiguration()->get( $name );
	    } else {
		    return Application::getInstance()->getConfiguration()->get( $name );
	    }

    }

    function rf_getimagesize($file) {

        return @getimagesize($file);

    }

    /**
     * Display debug vars
     *
     * @return ApplicationConfigurationParameterSet|mixed
     */
    function rf_debug_display() {

        $debugVars = Application::getInstance()->getDebugVars();

        foreach($debugVars as $debugVar) {
            var_dump($debugVar);
        }

    }

    /**
     * Add a var to the debug array if debug is activated
     *
     * @param mixed $var
     */
    function rf_debug($var) {

        if(rf_config('options.debug')) {
            Application::getInstance()->addDebugVar($var);
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

