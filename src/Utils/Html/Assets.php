<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Utils\Html;

/**
 * Class Assets
 *
 * @TODO: Allow custom base paths for js, css and images
 *
 * @package Rf\Core\Utils\Html
 */
class Assets {

    /** @var array $jsFiles */
    private static $jsFiles = [];

    /** @var array $cssFiles */
    private static $cssFiles = [];
    
    /**
     * Enqueue a script
     * Method 1: using "script-name.js" if the file is located in "/assets/js/"
     * Method 2: using the full url (https://...)
     *
     * @param string $file
     * @param string $version
     * @param bool $preload
     * @param array $params
     */
    public static function enqueueScript($file, $version = null, $preload = false, $params = []) {

        self::$jsFiles[$file] = [];

	    if(isset($version)) {
		    self::$jsFiles[$file]['version'] = $version;
	    }

	    self::$jsFiles[$file]['params'] = $params;

	    if($preload) {
		    self::$jsFiles[$file]['preload'] = true;
	    } else {
		    self::$jsFiles[$file]['preload'] = false;
	    }

    }
    
    /**
     * Get the enqueued scripts
     * As an array: array of the enqueued scripts (name|url)
     * As an HTML string: Generated HTML tags corresponding to the enqueued scripts
     *
     * @param bool $asHtml
     *
     * @return array|string
     */
    public static function getEnqueuedScripts($asHtml = false) {

        $fileList = array_keys(self::$jsFiles);

        if($asHtml) {

            if(!isset(self::$jsFiles)) {
            	return '';
            }

	        $jsFiles = [];

            foreach($fileList as $file) {

	            $params = '';
	            foreach(self::$jsFiles[$file]['params'] as $name => $value) {

		            if(is_string($name)) {
			            $params .= ' ' . $name . '="' . $value . '" ';
		            } else {
			            $params .= ' ' . $value . ' ';
		            }

	            }

                if(
                	strstr($file, 'http://') !== false
	                || strstr($file, 'https://') !== false
	                || substr($file, 0, 1) === '/'
                ) {

	                $jsFiles[] = '<script type="text/javascript" ' . $params . ' src="' . $file . '"></script>';

	                if(self::$jsFiles[$file]['preload']) {
		                header('Link: ' . $file . '; rel="preload"; as="script"', false);
	                }

                } else {

	                $url = '/assets/js/' . $file . (isset(self::$jsFiles[$file]['version']) ? '?v=' . self::$jsFiles[$file]['version'] : '');
	                $jsFiles[] = '<script type="text/javascript" ' . $params . ' src="' . $url . '"></script>';

	                if(self::$jsFiles[$file]['preload']) {
		                header('Link: ' . $url . '; rel="preload"; as="script"', false);
	                }

                }

            }

            return implode(PHP_EOL, $jsFiles) . PHP_EOL;

        } else {
            return self::$jsFiles;
        }

    }
    
    
    /**
     * Enqueue a stylesheet
     * Method 1: using "stylesheet-name.css" if the file is located in "/assets/css/"
     * Method 2: using the full url (https://...)
     *
     * @param string $file
     * @param string $version
     * @param bool $preload
     */
    public static function enqueueStylesheet($file, $version = null, $preload = false) {

        self::$cssFiles[$file] = [];

        if(isset($version)) {
            self::$cssFiles[$file]['version'] = $version;
        }

        if($preload) {
            self::$cssFiles[$file]['preload'] = true;
        } else {
	        self::$cssFiles[$file]['preload'] = false;
        }

    }
    
    /**
     * Get the enqueued stylesheets
     * As an array: array of the enqueued stylesheets (name|url)
     * As an HTML string: Generated HTML tags corresponding to the enqueued stylesheets
     *
     * @param bool $asHtml
     *
     * @return array|string
     */
    public static function getEnqueuedStylesheets($asHtml = false) {

        $fileList = array_keys(self::$cssFiles);

        if($asHtml) {

            if(empty($fileList)) {
            	return '';
            }

            $cssFiles = [];

            foreach($fileList as $file) {

                if(
                	strstr($file, 'http://') !== false
	                || strstr($file, 'https://') !== false
	                || substr($file, 0, 1) === '/'
                ) {

                    $cssFiles[] = '<link href="' . $file . '" rel="stylesheet" type="text/css"/>';

	                if(self::$cssFiles[$file]['preload']) {
		                header('Link: ' . $file . '; rel=preload; as=stylesheet', false);
	                }

                } else {

                	$url = '/assets/css/' . $file . (isset(self::$cssFiles[$file]['version']) ? '?v=' . self::$cssFiles[$file]['version'] : '');
                    $cssFiles[] = '<link href="' . $url . '" rel="stylesheet" type="text/css"/>';

	                if(self::$cssFiles[$file]['preload']) {
		                header('Link: ' . $url . '; rel=preload; as=stylesheet', false);
	                }

                }

            }

            return implode(PHP_EOL, $cssFiles) . PHP_EOL;

        } else {
            return self::$cssFiles;
        }

    }

    /**
     * Get target script uri
     *
     * @param string $filename Ex: my_script.js
     * @param string $subFolder Ex: my/sub/folder
     *
     * @return string
     */
    public static function getScriptUri($filename, $subFolder = '') {

        return '/assets/js/' . (!empty($subFolder) ? $subFolder . '/' : '') . $filename;

    }

    /**
     * Get target stylesheet uri
     *
     * @param string $filename Ex: my_style.css
     * @param string $subFolder Ex: my/sub/folder
     *
     * @return string
     */
    public static function getStylesheetUri($filename, $subFolder = '') {

        return '/assets/css/' . (!empty($subFolder) ? $subFolder . '/' : '') . $filename;

    }

    /**
     * Get target image uri
     *
     * @param string $filename Ex: my_image.jpeg
     * @param string $subFolder Ex: my/sub/folder
     *
     * @return string
     */
    public static function getImageUri($filename, $subFolder = '') {

        return '/assets/images/' . (!empty($subFolder) ? $subFolder . '/' : '') . $filename;

    }

}