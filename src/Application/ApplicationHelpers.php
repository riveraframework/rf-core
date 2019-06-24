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
    use Rf\Core\Base\ParameterSet;
    use Rf\Core\Service\ServiceProvider;
    use Rf\Core\Utils\Format\Json;

    /**
     * Get the running app instance
     *
     * @return ApplicationCli|ApplicationMvc
     */
    function rf_app() {

        if(APPLICATION_TYPE == ApplicationCli::TYPE) {
            return ApplicationCli::getApp();
        } else {
            return ApplicationMvc::getApp();
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

        return rf_sp()->getConfig()->get($name);

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

        if(rf_config('debug.enabled')) {
            rf_app()->addDebugVar($var);
        }

        if(rf_config('logging.enabled')) {

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
     * @TODO: Move or remove
     *
     * @param array $vars
     *
     * @return string
     */
    function rf_template_vars(array $vars) {

        return json_encode(array_values($vars));

    }

}
