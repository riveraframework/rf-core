<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Application\Components;

use Rf\Core\System\Performance\Benchmark;

/**
 * Class Controller
 * This class is meant to be extended in the application modules.
 *
 * @package Rf\Core\Application\Components
 *
 * @TODO: Add a getRequest() method
 * @TODO: Add a "filter system" in the template e.g: `{string | camel_case}`
 */
abstract class Controller {

    /** @var string $html Controller HTML content
     */
    public $html = '';

    /** @var array $dictionary Controller dictionary */
    public $dictionary = [];

    /** @var string $moduleName Module name */
    public $moduleName;

    /** @var string Module directory */
    public $moduleDir;

    /** @var array Controller options */
    public $options;

    /** @var string Default output format */
    public $defaultOutputFormat = 'html';

    /** @var string */
    public $language;

    /** @var bool */
    public $display = true;

    /** @var bool */
    public $forceCache = false;

    /** @var string  */
    public $forceCacheFileName;

    /** @var array  */
    public $cacheConfig = []; // ['cache_identifiers', 'path' => 'path-or-key', 'duration' => 3600]

    /** @var array Available actions */
    public $actions = [];

    /** @var string Default view for the current controller */
    public static $defaultView = 'page';

    /** @var array List of all available modules (+ forced) */
    public static $moduleList = [];

    /** @var array List of all forced modules */
    public static $moduleListForced = [];

    /** @var array Export data to be reused in other views/controllers */
    protected static $export = [];

    /**
     * Create a new controller
     *
     * @param array $dictionary
     */
    public function __construct(array $dictionary = []) {

        $calledClassName = get_class($this);
        $calledClassNameParts = explode('\\', $calledClassName);

        $this->moduleName = lcfirst($calledClassNameParts[count($calledClassNameParts) - 2]);
        $this->moduleDir = rf_dir('modules') . $this->moduleName . '/';

        // Set default language
        if(!isset($this->language)) {
            $this->language = rf_current_language();
        }

        // Set default dictionary
        if(!empty($dictionary)) {
            $this->dictionary = $dictionary;
        }

    }

    /**
     * Get the controller as a string (HTML)
     *
     * @return string
     */
    final public function __toString() {

        return $this->html;

    }

    /* ####################################################################### */
    /* ###########################  TEMPLATES  ############################### */
    /* ####################################################################### */

    /**
     * Get a partial view
     *
     * @param string $partialName
     * @param array $dictionary
     *
     * @return string
     */
    final public function getPartial($partialName, $dictionary = []) {

        if(!empty($dictionary)) {
            $this->dictionary = $dictionary;
        }

        $methodName = 'partial' . str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $partialName)));

        if(method_exists($this, $methodName)) {

            $this->$methodName();

        } else {

            $this->loadTemplate('partials:' . $partialName);

        }

        return $this->html;

    }

    /**
     * This function load the template file, then use buffer to replace PHP values,
     * then if I18n is active replace all translatable strings.
     *
     * @param string $viewName
     */
    public function loadTemplate($viewName) {

        $normalFile = $this->moduleDir . '/views/' . str_replace(':', '/', $viewName) . '.php';
        $mobileFile = $this->moduleDir . '/views/' . str_replace(':', '/', $viewName) . '.mobile.php';

        if(rf_request()->isMobile() && file_exists($mobileFile)) {
            $viewFile = $mobileFile;
        } else {
            $viewFile = $normalFile;
        }

        // Get template path
        if(file_exists($viewFile)) {

            // Replace translatable strings
            $this->html = $this->translate($viewFile);

            // Load included templates
            $this->loadIncludes();

            // Cache the generated code
            $this->cache();

        } else {

            $this->error(404, $viewFile);

        }

    }

    /**
     * This function load the template file, then use buffer to replace PHP values,
     * then if I18n is active replace all translatable strings.
     *
     * @param string $moduleName
     * @param string $viewName
     */
    public function loadExternalTemplate($moduleName, $viewName) {

        $normalFile = rf_dir('modules') . $moduleName . '/views/' . str_replace(':', '/', $viewName) . '.php';
        $mobileFile = rf_dir('modules') . $moduleName . '/views/' . str_replace(':', '/', $viewName) . '.mobile.php';

        if(rf_request()->isMobile() && file_exists($mobileFile)) {
            $viewFile = $mobileFile;
        } else {
            $viewFile = $normalFile;
        }

        // Get template path
        if(file_exists($viewFile)) {

            // Replace translatable strings
            $this->html = $this->translate($viewFile);

            $this->loadIncludes();

            // Load included templates
            $this->loadIncludes();

            // Cache the generated code
            $this->cache();

        } else {
            $this->error(404, $viewFile);
        }

    }

    /**
     * Translate all translatable variable in the target file
     *
     * @param string $viewFile
     *
     * @return string
     */
    final protected function translate($viewFile) {

        // Replace PHP values
        ob_start();
        include $viewFile;
        $fileContent = ob_get_clean();

        // Replace translatable strings
        $matches = [];
        if(preg_match_all('/{{([^{{]*)}}/s', $fileContent, $matches)) {

            if(count($matches[1])) {

                $cleanViewName = str_replace('.mobile', '', substr(basename($viewFile), 0 , strlen(basename($viewFile)) - 4));
                $transFile = dirname($viewFile) . '/translations/' . basename($cleanViewName) . '.php';

                if(file_exists($transFile)) {
                    $trans = include $transFile;
                } else {
                    $trans = [];
                }

                foreach ($matches[1] as $string) {

                    $stringParts = explode('|', $string);
                    //$filter = !empty($stringParts[1]) ? $stringParts[1] : false;
                    $transParts = explode(':', $stringParts[0], 2);
                    $transKey = $transParts[0];
                    $transParams = !empty($transParts[1]) ? json_decode($transParts[1]) : [];

                    // Get translated string
                    if(isset($trans[trim($transKey)][$this->language])) {

                        $newString = preg_replace('/(\r|\n)+/', '', vsprintf($trans[trim($transKey)][$this->language], $transParams));
                        $newString = preg_replace('/(\s+)/', ' ', $newString);

                    } else {

                        $newString = $transKey;
                    }

                    // Replace
                    $fileContent = str_replace('{{' . $string . '}}', nl2br($newString), $fileContent);
                }

            }

        }

        return $fileContent;

    }

    /**
     * Load included files in the template
     */
    final protected function loadIncludes() {

        $matches = [];
        if(preg_match_all('/{%([^{%]*)%}/s', $this->html, $matches)) {

            foreach ($matches[1] as $string) {

                $commandParts = explode(' ', trim($string));

                if($commandParts[0] == 'include' && !empty($commandParts[1])) {

                    $includeParts = explode(':', $commandParts[1]);

                    if(!empty($includeParts[0]) && !empty($includeParts[1])) {

                        $className = $includeParts[0];
                        $partialName = $includeParts[1];

                        /** @var Controller $controller */
                        $controller = new $className($this->dictionary);

                        // Retrieve the rendered partial
                        $html = $controller->getPartial($partialName);

                        // Replace placeholder by the generated code
                        $this->html = str_replace('{%' . $string . '%}', $html, $this->html);

                    }

                }

            }

        }

    }

    /**
     * Set controller html
     *
     * @param string $html
     */
    public function setHtml($html) {

        $this->html = $html;

    }

    /**
     * Get controller generated html
     *
     * @return string
     */
    public function getHtml() {

        return $this->html;

    }

    /**
     * Get the controller content
     *
     * @param string $viewName
     */
    protected function render($viewName) {

        $this->loadTemplate($viewName);

        if(
            rf_config('debug.active')
            && (
                !rf_request()->isAjax()
                || (rf_request()->isAjax() && rf_config('debug.ajax'))
            )
        ) {

            rf_debug_display();

        }

        if(!rf_request()->isAjax() && rf_config('debug.benchmark')) {

            echo 'Execution time: ' . (microtime(true) - APPLICATION_START) . 's';
            Benchmark::display();

        }

        $this->display();

    }

    /**
     * Echo the generated HTML content
     */
    protected function display() {

        echo $this;
        die;

    }

    /**
     * Write data to a cached file
     */
    public function cache() {

        $cacheIdentifiers = $this->getCacheIdentifiers();

        // Skip caching if no identifier is provided
        if(
            !$cacheIdentifiers
            || empty($this->cacheConfig['path'])
        ) {
            return;
        }

        // @TODO: Check if key or path works with disk handler
        $cacheKey = $this->cacheConfig['path'];
        $duration = !empty($this->cacheConfig['duration']) ? $this->cacheConfig['duration'] : 60 * 60;

        // @TODO: Also cache data type? (e.g: xml,json,etc. templates)
        rf_cache_set($cacheKey, $this->getHtml(), $duration, $cacheIdentifiers);

    }

    /**
     * Get the cache identifiers.
     *
     *
     * @return array|bool|mixed
     */
    public function getCacheIdentifiers() {

        if(empty($this->cacheConfig['cache_identifiers'])) {

            return false;

        } else if(!is_array($this->cacheConfig['cache_identifiers'])) {

            return [$this->cacheConfig['cache_identifiers']];

        } else {

            return $this->cacheConfig['cache_identifiers'];

        }

    }

    /**
     * Render the cached version if available
     *
     * @return bool
     */
    public function cached() {

        $cacheIdentifiers = $this->getCacheIdentifiers();

        // Skip caching if no identifier is provided
        if(
            !$cacheIdentifiers
            || empty($this->cacheConfig['path'])
        ) {
            return false;
        }

        $cacheKey = $this->cacheConfig['path'];

        $cached = rf_cache_get($cacheKey, $cacheIdentifiers);

        if(!empty($cached)) {

            $this->html = $cached;

            return true;

        }

        return false;

    }

    /**
     * Minify HTML
     */
    final protected function minifyHTML() {

    }

    /**
     * Replace the actual HTML content by an error view
     *
     * @param int|string $errorCode Error code/view
     * @param string $errorContent Error content
     */
    final protected function error($errorCode, $errorContent = '') {

        switch ($errorCode) {

            case 404:
                $this->html = 'Controller error: Unable to load the template' . (rf_config('debug.active') ? '<br/>' . $errorContent : '');
                break;

            default:
                $this->html = 'Controller error: Unknown error';
                break;

        }

    }

}