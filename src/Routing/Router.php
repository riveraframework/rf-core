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

use Rf\Core\Base\ParameterSet;
use Rf\Core\Http\QueryParameterSet;
use Rf\Core\Http\Response;
use Rf\Core\I18n\I18n;
use Rf\Core\Uri\CurrentUri;
use Rf\Core\Uri\Uri;

/**
 * Class Router
 *
 * @package Rf\Core\Routing
 */
class Router {

    /** @var array Array filled with all available routes for requests */
    public $routes = [];

    /** @var array Array filled with all available routes for links */
    public $routesForLinks = [];

    /** @var Route Current route */
    public $currentRoute;

    /**
     * @var array Default redirect urls
     *
     * @TODO: custom urls + exec redirect
     */
    public static $redirectionUrls = [
        '403' => '',
        '404' => ''
    ];

    /**
     * Init routes
     */
    public function initRequestRoutes() {

        $routingFiles = glob(rf_dir('modules') . '/*/config/routing.php');
        foreach ($routingFiles as $routingFile) {
            $moduleRoutes = include $routingFile;
            $this->routes = $this->routes + $moduleRoutes;
        }

        $this->routesForLinks = $this->routesForLinks + $this->routes;

    }

    /**
     * Init routes
     *
     * @param string $moduleDir
     */
    public function addRoutesForLinks($moduleDir) {

        $routingFiles = glob($moduleDir . '/*/config/routing.php');
        foreach ($routingFiles as $routingFile) {
            $moduleRoutes = include $routingFile;
            $this->routesForLinks = $this->routesForLinks + $moduleRoutes;
        }

    }

    /**
     * Get the current route
     *
     * @return array
     */
    public function getCurrentRoute() {

        return $this->currentRoute;

    }

    /**
     * Set the current route
     *
     * @param Route $route
     */
    public function setCurrentRoute(Route $route) {

        $this->currentRoute = $route;

    }

    /**
     * Main routing process.
     * Determine if one or more route is applicable and apply the right route using the current uri.
     */
    public function route() {

        if(empty($this->routes)) {
            $this->initRequestRoutes();
        }

        rf_debug(CurrentUri::getQuery());

        foreach ($this->routes as $routeName => $route) {

//            $routeRegex = '/^' . str_replace('/', '\/', preg_replace('/\{\w+\}/', '(\w+)', $route['pattern'])) . '$/ui';
            $routeRegex = '/^' . str_replace('/', '\/', preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $route['pattern'])) . '$/ui';
            $urlIsApplicable = (bool) preg_match($routeRegex, '/' . CurrentUri::getQuery(), $matches);

	        // @TODO: Check accepted methods

            if($urlIsApplicable) {

	            $methodIsApplicable = (
            		empty($route['methods'])
		            || rf_request()->getMethod() == $route['methods']
		            || in_array(rf_request()->getMethod(), $route['methods'])
	            );

	            if($methodIsApplicable) {

	            	if(!empty($route['redirect-route'])) {

	            		$args = !empty($route['defaults']) ? $route['defaults'] : [];
			            $redirectCode = !empty($route['redirect-code']) ? $route['redirect-code'] : null;

	            		self::redirect($this->link_to($route['redirect-route'], $args), $redirectCode);

		            }

		            $route['name'] = $routeName;
	                $this->currentRoute = $route;

		            foreach ($matches as $key => $value) {
			            if (is_int($key)) {
				            unset($matches[$key]);
			            }
		            }

	                $this->buildRequestQuery($this->currentRoute, $matches);
	                return;

	            }

            }

        }

        if(empty($this->currentRoute)) {

            if(rf_request()->isAjax()) {
                $response = new Response(404);
                $response->send();
            } else {
                // @TODO: Set flag or add errors route ton config
                // @TODO: Get Error type from above conditions -> 401 vs 404
                $response = new Response(404);
                $response->send();
            }

        }

    }
    
    /**
     * Build the request query matching route available params
     * 
     * @param array $route
     * @param array $args
     */
    protected function buildRequestQuery(array $route, array $args = []) {

        I18n::setCurrentLanguage($route['defaults']['language']);

        foreach($route['defaults'] as $name => $value) {
            rf_request_query()->set($name, $value);
        }

        // Force language using the GET parameter
        // @TODO: As an option?
        if(!rf_empty(rf_request()->getGetData()->get('language'))) {
            I18n::setCurrentLanguage(rf_request()->getGetData()->get('language'));
            rf_request_query()->set($name, rf_request()->getGetData()->get('language'));
        }

        foreach ($args as $name => $value) {
            rf_request_query()->set($name, $value);
        }

    }
    
    /**
     * Reset the query for current request
     *
     * @return void
     */
    public function resetQuery() {

        rf_request()->set('query', new QueryParameterSet([]));

    }

    /**
     * @param null $routeName
     * @param array $args
     *
     * @return string
     */
    public function link_to($routeName = null, $args = []) {

        // @TODO: If debug on -> exception

        $link = '#';

        if(!empty($this->routesForLinks[$routeName]['pattern'])) {
            $link = $this->routesForLinks[$routeName]['pattern'];
        }

        if(!empty($routeName)) {

        	// @TODO: Remove api_
        	if(strpos($routeName, 'api_') === 0) {

		        $moduleRouteName = $routeName;

		        // Force the current language for API urls
		        if(empty($args['language'])) {
			        $args['language'] = rf_current_language();
		        }

	        } else {

		        $moduleRouteName = $routeName . '_';
		        if(!empty($args['language'])) {
			        $moduleRouteName .= $args['language'];
			        unset($args['language']);
		        } else {
			        $moduleRouteName .= rf_current_language();
		        }
	        }

            if(!empty($this->routesForLinks[$moduleRouteName]['pattern'])) {
                $link = $this->routesForLinks[$moduleRouteName]['pattern'];
            }

            // Replace pattern vars with params
            if(is_array($args) && !empty($args)) {

                foreach($args as $name => $value) {

                    $mask = '{' . $name . '}';

                    if(strpos($link, $mask) !== false) {

                        $link = str_replace('{' . $name . '}', $value, $link);
                        unset($args[$name]);

                    }

                }

            }

            // Add query string to the link
            Uri::addQueryStringToUri((array)$args, $link);

            // @TODO: Add domain

        } elseif(!empty($args['language'])) {

        	// Switch language process
        	// Get current route info and add target language
        	$currentRoute = $this->getCurrentRoute();
	        $newArgs = rf_request_query()->toArray();
	        $newArgs['language'] = $args['language'];
	        $newRouteName = substr($currentRoute['name'], 0, -3);

	        return $this->link_to($newRouteName, $newArgs);

        }

        return $link;

    }

    /**
     * Check if the current domain is in the available domain list of the configuration file
     */
    public function testDomain() {

        /** @var false|string|ParameterSet $availableDomainsList */
        $availableDomainsList = rf_config('app.available-domains');

        if (!$availableDomainsList) {
            return;
        }

        if(is_string($availableDomainsList)) {
            $availableDomains = explode(',', $availableDomainsList);
        } else {
            $availableDomains = $availableDomainsList->toArray();
        }
        $currentDomain = CurrentUri::getDomain();

        foreach ($availableDomains as $domain) {

            if (in_array($domain, array('*.' . $currentDomain, CurrentUri::getHost()))) { // !!!!
                return;
            }

        }

        $response = new Response(403);
        $response->setBody(
            'Unavailable domain: ' . $currentDomain . '<br/>' .
            'Available domains: ' . $availableDomainsList);
        $response->send();

    }

    /**
     * 
     * @param string $type 301|302
     * @return void
     *
     * @TODO: redirect 301/302 and use Http Response
     */
    public static function redirect($url, $type = null) {
        header('Location: ' . $url);
        exit;
    }

}
