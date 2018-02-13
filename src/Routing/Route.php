<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Routing;

/**
 * Class Route
 *
 * @since 1.0
 * 
 * @package Rf\Core\Routing
 */
class Route {

    /**
     * @var string $name Route name
     * @since 1.0
     */
    public $name;

    /**
     * @var string $domain Applicable domain(s) domain1|domain2|...
     * @since 1.0
     */
    public $domain = '';

    /**
     * @var string $url Route uri pattern
     * @since 1.0
     */
    public $url = '';

    /**
     * @var array $params Available parameters
     * @since 1.0
     */
    public $params = array(
        'mandatory' => array(),
        'optional' => array()
    );

    /**
     * array(
     *     'filtername' => array(optional|mandatory, regex, precedence, check function),
     *     ...
     * )
     * @var array $filters Route filters
     * @since 1.0
     */
    public $filters = array();

    /**
     * @var array $defaults Default values for filters
     * @since 1.0
     */
    public $defaults = array();

    /**
     * @var bool $supportRoot Enable/disable the support of root uri (/)
     * @since 1.0
     */
    public $supportRoot = false;
    
    /**
     * @var boolean $supportTranslation Enable/disable translation support (Only available for modules, views and subviews)
     * @since 1.0
     */
    public $supportTranslation = false;

    /**
     * @var array $translationModuleExceptions List of modules for which the translation doesn't apply
     * @since 1.0
     */
    public $translationModuleExceptions = array();

    /**
     * @var bool $forceDomain Force a specific domain for link generation
     * @since 1.0
     */
    public $forceDomain = false;

    /**
     * @var array $allowedModules List of allowed modules for the route
     * @since 1.0
     */
    public $allowedModules = array();

    /**
     * @var string $regex Regex to determine if the route is applicable
     * @since 1.0
     */
    public $regex = '';

    /**
     *
     * @since 1.0
     */
    const MANDATORY_PARAM = 1;

    /**
     *
     * @since 1.0
     */
    const OPTIONAL_PARAM = 0;

    /**
     * Get the route name
     *
     * @return string
     */
    public function getName() {

        return $this->name;

    }

    /**
     * Set the route name and add it to the Router route list
     *
     * @since 1.0
     * 
     * @param $name Route name
     * @return bool
     */
    public function set($name) {
        
        $this->name = $name;
        
        if (empty($this->domain)) {
            $this->domain = '*';
        }
        
        if (count($this->params['mandatory']) + count($this->params['optional']) !== count($this->filters)) {
            return false;
        }
        
        Router::$routes[$name] = $this;
        
        return true;
    }

    /**
     * Allow the current route to one or multiple domains or get the value
     *
     * @since 1.0
     *
     * @param array|string $domain
     * @return \Rf\Core\Routing\Route|string
     */
    public function applyTo($domain = null) {

        if (isset($domain)) {

            if (is_array($domain)) {
                $domain = implode('|', $domain);
            }

            $this->domain = $domain;

            return $this;

        } else {

            return $this->domain;

        }

    }

    /**
     * Set Route uri pattern
     *
     * @since 1.0
     * 
     * @param $pattern Route uri pattern
     * @return \Rf\Core\Routing\Route
     */
    public function uri($pattern) {

        $this->url = str_replace(array('[', ']'), array('<', '>'), $pattern);

        return $this;

    }

    /**
     * Set Route filters
     *
     * @since 1.0
     * 
     * @param array $filters array('paramName' => array(
     *     param_type (int), 
     *     regex (string), 
     *     precendence (string), 
     *     cust_check_func (string) must return false on failure
     * ), ...)
     * 
     * @return \Rf\Core\Routing\Route
     */
    public function filters($filters) {

        foreach ($filters as $name => &$filter) {

            // Generate list of parameter regarding the type (mandatory|optional)
            if ($filter[0] === self::MANDATORY_PARAM) {
                $this->params['mandatory'][] = $name;
            } elseif ($filter[0] === self::OPTIONAL_PARAM) {
                $this->params['optional'][] = $name;
            }

            // Determine precedence
            if (empty($filter[2])) {
                $mandatoryParams = array_values($this->params['mandatory']);
                $lastMandatoryParam = end($mandatoryParams);
                $filter[2] = !$lastMandatoryParam ? null : $lastMandatoryParam;
            }
        }

        // Set filters
        $this->filters = $filters;

        return $this;
    }

    /**
     * Set default values for query params
     *
     * @since 1.0
     * 
     * @param array $defaults
     * @return \Rf\Core\Routing\Route
     */
    public function defaults($defaults) {

        $this->defaults = $defaults;

        return $this;

    }
    
    /**
     * Allow/Disallow support of /
     *
     * @since 1.0
     * 
     * @param boolean $rootSupport
     * @return \Rf\Core\Routing\Route
     */
    public function rootSupport($rootSupport = null) {

        if (isset($rootSupport)) {

            $this->supportRoot = $rootSupport;

            return $this;

        } else {

            return $this->supportRoot;

        }
    }
    
    /**
     * Allow/Disallow support of translation for modules, views and subviews
     *
     * @since 1.0
     * 
     * @param boolean $translationSupport
     * @return \Rf\Core\Routing\Route
     */
    public function translationSupport($translationSupport = null, $modulesExceptions = array()) {

        if (isset($translationSupport)) {

            $this->supportTranslation = $translationSupport;
            $this->translationModuleExceptions = $modulesExceptions;

            return $this;

        } else {

            return $this->supportTranslation;

        }

    }
    
    /**
     * Force given domain for link generation
     *
     * @since 1.0
     * 
     * @return \Rf\Core\Routing\Route
     */
    public function forceDomainForLink() {

        $this->forceDomain = true;

        return $this;

    }
    
    /**
     * Return the value of forceDomain
     *
     * @since 1.0
     * 
     * @return boolean
     */
    public function domainForce() {

        return $this->forceDomain;

    }
    
    /**
     * Prevent disallowed modules usage for current route
     *
     * @since 1.0
     * 
     * @param array|string $moduleList
     * @return Route|array
     */
    public function allowedModules($moduleList = []) {

        if (!empty($moduleList)) {
            $this->allowedModules = $moduleList;
            return $this;
        } else {
            return $this->allowedModules;
        }

    }

    /**
     * Generate the regex matching the Route
     *
     * @since 1.0
     * 
     * @return string
     */
    public function generateRegex() {

        $this->regex = $this->url;
        $this->regex = str_replace('/', '\/', $this->regex);
        $this->regex = str_replace(')', ')?', $this->regex);
        $lastMandatoryParam = null;

        if (count($this->params['mandatory']) > 0) {

            foreach ($this->params['mandatory'] as $param) {
                list(, $regex) = $this->filters[$param];
                $this->regex = str_replace('<' . $param . '>', '(?P<' . $param . '>' . $regex . ')', $this->regex);
                $lastMandatoryParam = $param;
            }

        }

        if (count($this->params['optional']) > 0) {

            foreach (array_reverse($this->params['optional']) as $param) {
                list(, $regex, $precedence) = $this->filters[$param];
                $this->regex = str_replace('<' . $param . '>', '(?P<' . $param . '>' . $regex . ')', $this->regex);
            }

        }

        $this->regex = '/^' . $this->regex . '$/i';
        return $this->regex;
    }

    /**
     * This function generate a clean uri with given parameters
     *
     * @since 1.0
     * 
     * @param array $params array(array(name, value), ...n)
     * @return string
     */
    public function generateUri($params) {
        
        $mandatoryParamList = $this->params['mandatory'];
        $optionalParamList = $this->params['optional'];

        $mandatoryToReplaceCount = 0;
        $paramsToReplace = array();
        
        foreach ($params as $param => $value) {

            if (in_array($param, $mandatoryParamList)) {

                if (preg_match ('/^' . $this->filters[$param][1] . '$/', $value)) {
                    $paramsToReplace[$param] = array($value, $this->filters[$param][2]);
                    $mandatoryToReplaceCount++;
                } elseif(!empty($this->defaults[$param])) {
                    $paramsToReplace[$param] = array($this->defaults[$param], $this->filters[$param][2]);
                    $mandatoryToReplaceCount++;
                }

            } elseif (in_array($param, $optionalParamList)) {

                if (preg_match ('/^' . $this->filters[$param][1] . '$/', $value)) {
                    $paramsToReplace[$param] = array($value, $this->filters[$param][2]);
                } elseif(!empty($this->defaults[$param])) {
                    $paramsToReplace[$param] = array($this->defaults[$param], $this->filters[$param][2]);
                } else {
                    $paramsToReplace[$param] = array('', '');
                }

            }

        }
        
        if($mandatoryToReplaceCount !== count($mandatoryParamList)) {

            if(!isset($paramsToReplace[Router::DEFAULT_CONTROLLER_PARAM]) && !empty($this->defaults[$param])) {
                // It's OK
            } else {
                // throw exception and return default
                return;
            }

        }
            
        // Sanitize uri part to replace
        $tmpUriParts = preg_split('/(\(|\))/', $this->url);

        foreach($tmpUriParts as $key => $part) {

            if(empty($tmpUriParts[$key])) {
                unset($tmpUriParts[$key]);
            }

        }

        // create copy of param set to keep informations
        $paramsToReplaceCopy = $paramsToReplace;

        // Initialize array with final parts
        $uriParts = array();

        foreach($paramsToReplaceCopy as $name => $param) {

            list($value, $precedence) = $param;

            foreach($tmpUriParts as $key => $part) {

                if(strpos($tmpUriParts[$key], '<' . $name . '>') !== false) {

                    if(empty($value)) {
                        unset($tmpUriParts[$key]);
                        unset($paramsToReplaceCopy[$name]);
                        break;
                    } else {

                        $hasPrecedence = empty($precedence) || !empty($paramsToReplace[$precedence][0]);

                        if($hasPrecedence) {
                            $uriParts[$key] = str_replace('<' . $name . '>', $value, $tmpUriParts[$key]);
                            unset($paramsToReplaceCopy[$name]);
                            break;
                        } else {
                            unset($tmpUriParts[$key]);
                            unset($paramsToReplaceCopy[$name]);
                            break;
                        }

                    }

                }

            }

        }

        return implode('', $uriParts);

    }

}
