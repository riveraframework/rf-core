<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Route;

use Rf\Core\Base\Exceptions\ErrorMessageException;
use Rf\Core\Base\ParameterSet;
use Rf\Core\Base\Uri;
use Rf\Core\Http\Request;
use Rf\Core\Http\Response;
use Rf\Core\Http\ResponseInterface;
use Rf\Core\Service\Service;
use Rf\Core\System\Performance\Benchmark;
use Rf\Core\Utils\Debug\ErrorHandler;

/**
 * Class Router
 *
 * @package Rf\Core\Route
 */
class RouterService extends Service {

    /** @var string  */
    const TYPE = 'router';

    /** @var RouterConfiguration */
    protected $configuration;

    /** @var Route[] Available routes for routing (indexed by name) */
    protected $routesForRouting = [];

    /** @var Route[] Custom routes for links (indexed by name) */
    protected $routesForLinks = [];

    /** @var Route Current route */
    protected $currentRoute;

    /**
     * {@inheritDoc}
     *
     * @param array $configuration
     */
    public function loadConfiguration(array $configuration) {

        $this->configuration = new RouterConfiguration($configuration);

    }

    /**
     * {@inheritDoc}
     *
     * @return RouterConfiguration
     */
    public function getConfiguration() {

        return $this->configuration;

    }

    /**
     * Start the application execution using the main controller property
     *
     * @TODO: Add user customizable error handler
     * @TODO: Add response post-processing e.g: ->after($response)
     *
     * @param Request $request
     *
     * @throws \Exception
     */
    public function handleRequest(Request $request) {

        Benchmark::log('hook:before_handle_request:start');

        $hooks = rf_app()->getHooks('before_handle_request');
        foreach($hooks as $hook) {

            $hook($request);

        }

        Benchmark::log('hook:before_handle_request:end');

        Benchmark::log('handle request start');

        try {

            // Get the applicable route
            $route = $this->getCurrentRoute();

            // Get the controller name
            $controllerName = $route->getController();

            // Create the controller instance
            $controller = new $controllerName();

            // Get the action name
            $actionName = $route->getAction();

            // Execute the requested action
            if(method_exists($controller, 'wrapper')) {

                Benchmark::log($controllerName . '::' . $actionName . ' started (with wrapper)');
                $response = $controller->wrapper($actionName);

            } elseif(method_exists($controller, $actionName)) {

                Benchmark::log($controllerName . '::' . $actionName . ' started');
                $response = $controller->$actionName();

            } else {
                throw new \Exception('The requested action does not exist');
            }

            if(is_a($response, ResponseInterface::class)) {

                Benchmark::log('hook:after_handle_request:start');

                // @TODO: replace by a function execute hooks including the loop
                $hooks = rf_app()->getHooks('after_handle_request');
                foreach($hooks as $hook) {

                    $hook($response);

                }

                Benchmark::log('hook:after_handle_request:end');

                /** @var ResponseInterface $response */
                $response->send();
            }

        } catch(\Error $error) {

            if(
                rf_config('debug.enabled')
                && (
                !rf_request()->isAjax())
                || (rf_request()->isAjax() && rf_config('debug.ajax'))
            ) {

                echo 'Execution time: ' . (microtime(true) - APPLICATION_START) . 's';
                rf_debug_display();

                echo ErrorHandler::formatError($error);

            } elseif(rf_request()->isApi()) {

                throw new ErrorMessageException($error->getMessage());

            } else {

                throw new \Exception($error->getMessage(), $error->getCode(), $error);

            }

        }

    }

    /**
     * Init routes
     *
     * @throws \Exception
     */
    public function initRequestRoutes() {

        $prefix = $this->isDefault() ? '' : $this->getName() . '.';

        // Add routes for routing
        $routingFiles = glob(rf_dir('modules') . '/*/config/' . $prefix . 'routing*.php');
        foreach ($routingFiles as $routingFile) {
            $moduleRoutingRoutes = include $routingFile;
            foreach($moduleRoutingRoutes as $routeName => $routeParams) {
                $this->routesForRouting[$routeName] = new Route($routeName, $routeParams);
            }
        }

        // Add route for links
        $linksFiles = glob(rf_dir('modules') . '/*/config/' . $prefix . 'links*.php');
        foreach ($linksFiles as $linksFile) {
            $moduleLinksRoutes = include $linksFile;
            foreach($moduleLinksRoutes as $routeName => $routeParams) {
                $this->routesForLinks[$routeName] = new Route($routeName, $routeParams);
            }
        }

    }

    /**
     * Get the current route
     *
     * @return Route
     * @throws \Exception
     */
    public function getCurrentRoute() {

        if(!isset($this->currentRoute)) {
            $this->route();
        }

        return $this->currentRoute;

    }

    /**
     * Set the current route
     *
     * @param array $route
     */
    public function setCurrentRoute(array $route) {

        $this->currentRoute = $route;

    }

    /**
     * Main routing process.
     * Determine if one or more route is applicable and apply the right route using the current uri.
     *
     * @throws \Exception
     */
    public function route() {

        if(empty($this->routesForRouting)) {

            // Retrieve the available routes
            $this->initRequestRoutes();

        }

        foreach ($this->routesForRouting as $routeName => $route) {

            // Check if the request uri matches the route
            $uriMatches = $route->matchUri('/' . rf_request()->getUri()->query(), $foundParams);

            if($uriMatches) {

                // Check if the request method matches the route
	            $methodMatches = $route->matchMethod(rf_request()->getMethod());

	            if($methodMatches) {

	                // @TODO: Keep?
//	            	if(!empty($route['redirect-route'])) {
//
//	            		$args = !empty($route['defaults']) ? $route['defaults'] : [];
//			            $redirectCode = !empty($route['redirect-code']) ? $route['redirect-code'] : null;
//
//	            		self::redirect($this->link_to($route['redirect-route'], $args), $redirectCode);
//
//		            }

	                $this->currentRoute = $route;

		            foreach ($foundParams as $key => $value) {
			            if (is_int($key)) {
				            unset($foundParams[$key]);
			            }
		            }

	                $this->buildRequestQuery($this->currentRoute, $foundParams);
		            $this->updateLanguage();
	                return;

	            }

            }

        }

        if(empty($this->currentRoute)) {

            // @TODO: Set flag or add errors route ton config
            // @TODO: Get Error type from above conditions -> 401 vs 404
            // @TODO: Use standard name for error routes, e.g: error_404(_{lang})
            // @TODO: Allow default route redirect configuration, e.g: home

            if(rf_request()->isAjax()) {
                $response = new Response(404);
                $response->send();
            } else {
                $response = new Response(404);
                $response->send();
            }

        }

    }
    
    /**
     * Build the request query matching route available params
     * 
     * @param Route $route
     * @param array $params
     */
    protected function buildRequestQuery(Route $route, array $params = []) {

        // Add parameters default values to the query object
        $defaults = $route->getDefaults();
        foreach($defaults as $name => $value) {
            rf_request_query()->set($name, $value);
        }

        // Add custom params to the query object
        foreach ($params as $name => $value) {
            rf_request_query()->set($name, $value);
        }

    }

    /**
     * Update the language
     */
    protected function updateLanguage() {

        // Get the current language
        if(rf_request()->getGetData()->get('language')) {
            $language = rf_request()->getGetData()->get('language');
        } elseif(!empty($defaults['language'])) {
            $language = $defaults['language'];
        } else {
            $language = rf_current_language();
        }

        // Update the current language
        I18n::setCurrentLanguage($language);

        // Update the language in the query object
        rf_request_query()->set('language', $language);

    }
    
    /**
     * Reset the query for current request
     */
    public function resetQuery() {

        rf_request()->set('query', new ParameterSet([]));

    }

    /**
     *
     * Generate a link from a route name or the current route using the provided params or the current request params
     *
     * @param string|null $routeName
     * @param array $args
     *
     * @return string
     */
    public function link_to($routeName = null, $args = []) {

        $link = '#';

        if(!empty($routeName)) {

            if(!empty($this->routesForRouting[$routeName])) {

                $link = $this->routesForRouting[$routeName]->getPattern();

            } elseif(!empty($this->routesForLinks[$routeName])) {

                $link = $this->routesForLinks[$routeName]->getPattern();

            } else {

                $i18nRouteName = $routeName . '_';
                if(!empty($args['language'])) {
                    $i18nRouteName .= $args['language'];
                    unset($args['language']);
                } else {
                    $i18nRouteName .= rf_current_language();
                }

                if(!empty($this->routesForRouting[$i18nRouteName])) {

                    $link = $this->routesForRouting[$i18nRouteName]->getPattern();

                } elseif(!empty($this->routesForLinks[$i18nRouteName])) {

                    $link = $this->routesForLinks[$i18nRouteName]->getPattern();

                } else {

                    return $link;

                }

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

        	// Switch language process (no route name provided, only a language)
            // This will only work with route using the language suffix
            // e.g: my_current_route_en

            // Create new route params
	        $newArgs = rf_request_query()->toArray();
	        $newArgs['language'] = $args['language'];

            // Get new route name
            $currentRoute = $this->getCurrentRoute();
	        $newRouteName = substr($currentRoute->getName(), 0, -3);

	        return $this->link_to($newRouteName, $newArgs);

        }

        return $link;

    }

    /**
     * Check if the domain for the current request is in the available domain list of the configuration file
     *
     * @param array $domains
     */
    public function testDomain(array $domains) {

        /** @var false|string|ParameterSet $availableDomainsList */
        $availableDomainsList = $domains;

        if (empty($availableDomainsList)) {
            return;
        }

        if(is_string($availableDomainsList)) {
            $availableDomains = explode(',', $availableDomainsList);
        } else {
            $availableDomains = $availableDomainsList->toArray();
        }
        $currentDomain = rf_request()->getUri()->domain();

        foreach ($availableDomains as $domain) {

            if (in_array($domain, ['*.' . $currentDomain, rf_request()->getUri()->host()])) { // !!!!
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
     * Redirect
     * 
     * @param string $url
     * @param int $type 301|302
     * @param bool $return
     *
     * @return Response|void
     */
    public function redirect($url, $type = 302, $return = false) {

        $redirect = new Response($type);
        $redirect->addHeader('Location', $url);

        if($return) {
            return $redirect;
        } else {
            $redirect->send();
        }

    }

}
