<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//TODO: Add priority management for assets
namespace Rf\Core\Html;

/**
 * Class Assets
 *
 * @since 1.0
 *
 * @package Rf\Core\Html
 */
class Assets {

    /**
     * @var array $jsFiles
     * @since 1.0
     */
    private static $jsFiles = [];

    /**
     * @var array $cssFiles
     * @since 1.0
     */
    private static $cssFiles = [];
    
    /**
     * Cette fonction permet de définir un fichier JS à utiliser en indiquant son nom
     * (avec extension) si le fichier est dans le dossier "/web/asset/js" ou son adresse 
     * complète (http://...)
     *
     * @param string $file
     * @param string $version
     * @param bool $preload
     * @param array $params
     */
    public static function enqueueJsFile($file, $version = null, $preload = false, $params = []) {

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
     * Cette fonction retourne une chaine de caractères comprenant la liste des balises
     * correspondant aux fichier JS à charger.
     *
     * @since 1.0
     *
     * @param string $format 'array' (default) or 'html'
     * @return string 
     */
    public static function getJsFiles($format = 'array') {

        $fileList = array_keys(self::$jsFiles);

        if($format === 'html') {

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
     * Cette fonction permet de définir un fichier CSS à utiliser en indiquant son nom
     * (avec extension) si le fichier est dans le dossier de thème adapté ou son adresse 
     * complète (http://...)
     *
     * @param string $file
     * @param string $version
     * @param bool $preload
     */
    public static function enqueueCssFile($file, $version = null, $preload = false) {

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
     * Cette fonction retourne une chaine de caractères comprenant la liste des balises
     * correspondant aux fichier CSS à charger.
     *
     * @since 1.0
     *
     * @param string $format 'array' (default) or 'html'
     * @return string 
     */
    public static function getCssFiles($format = 'array') {

        $fileList = array_keys(self::$cssFiles);

        if($format === 'html') {

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
     * Get target JS file uri
     *
     * @since 1.0
     *
     * @param string $filename Ex: my_script.js
     * @param string $subfolder Ex: my/sub/folder
     * @return string
     */
    public static function getJsFileUri($filename, $subfolder = '') {
        return '/assets/js/' . (!empty($subfolder) ? $subfolder . '/' : '') . $filename;
    }

    /**
     * Get target CSS file uri
     *
     * @since 1.0
     *
     * @param string $filename Ex: my_style.css
     * @param string $subfolder Ex: my/sub/folder
     * @return string
     */
    public static function getCssFileUri($filename, $subfolder = '') {
        return '/assets/css/' . (!empty($subfolder) ? $subfolder . '/' : '') . $filename;
    }

    /**
     * Get target image file uri
     *
     * @since 1.0
     *
     * @param string $filename Ex: my_image.jpeg
     * @param string $subfolder Ex: my/sub/folder
     * @return string
     */
    public static function getImageFileUri($filename, $subfolder = '') {
        return '/assets/images/' . (!empty($subfolder) ? $subfolder . '/' : '') . $filename;
    }
}