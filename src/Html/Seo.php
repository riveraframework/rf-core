<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Html;

use Rf\Core\I18n\I18n;

/**
 * Class Seo
 *
 * @package Rf\Core\Configuration
 */
abstract class Seo {

    /** @var string $title SEO title */
    public static $title;

    /** @var string $description SEO Description */
    public static $description;

    /** @var string $tags SEO Tags */
    public static $tags;

    /** @var string $h1 SEO h1 */
    public static $h1;

    /** @var string $h1Subtitle SEO h1 subtitle */
    public static $h1Subtitle;

    /** @var string $canonicalUrl SEO canonical url */
    public static $canonicalUrl;

    /** @var string $alternateUrls SEO alternate urls */
    public static $alternateUrls;
    
    /**
     * Set the current page title tag: <title>
     *
     * @param string $title
     */
    public static function setTitle($title) {

    	self::$title = $title;

    }

    /**
     * Get the content of the current page title tag: <title>
     *
     * @return string
     */
    public static function getTitle() {

	    return isset(self::$title) ? self::$title : '';

    }
    
    /**
     * Set the current page h1 tag: <h1>
     *
     * @param string $h1
     */
    public static function setH1($h1) {

        self::$h1 = $h1;

    }
    
    /**
     * Get the current page h1 tag: <h1>
     *
     * @return string 
     */
    public static function getH1() {

	    return isset(self::$h1) ? self::$h1 : '';

    }
    
    /**
     * Set the current page h1 subtitle
     *
     * @param string $h1Subtitle
     */
    public static function setH1Subtitle($h1Subtitle) {

        self::$h1Subtitle = $h1Subtitle;

    }
    
    /**
     * Get the current page h1 subtitle
     *
     * @return string 
     */
    public static function getH1Subtitle() {

        return isset(self::$h1Subtitle) ? self::$h1Subtitle : '';

    }
    
    /**
     * Set the current page description meta
     *
     * @param string $description
     */
    public static function setDescription($description) {

        self::$description = $description;

    }
    
    /**
     * Set the current page description meta
     *
     * @return string 
     */
    public static function getDescription() {

	    return isset(self::$description) ? self::$description : '';

    }
    
    /**
     * Set the current page tag meta
     *
     * @param string|array $tags
     */
    public static function setTags($tags) {

        if(is_array($tags)) {
            self::$tags = implode(',', $tags);
        } else {
            self::$tags = $tags;
        }

    }
    
    /**
     * Get the current page tag meta
     *
     * @return string
     */
    public static function getTags() {

	    return isset(self::$tags) ? self::$tags : '';

    }

	/**
	 * Get the current page canonical link
	 *
	 * @param string $routeName
	 * @param array $args
	 */
    public static function setCanonicalUrl($routeName, array $args) {

    	$args['language'] = I18n::$defaultLanguage;

        self::$canonicalUrl = rf_config('app.url') . rf_link_to($routeName, $args);

    }

	/**
	 * Get the current page canonical link
	 *
	 * @return string
	 */
    public static function getCanonicalUrl() {

	    return isset(self::$canonicalUrl) ? self::$canonicalUrl : '';

    }

	/**
	 * Set alternate urls
	 *
	 * @param string $routeName
	 * @param array $args
	 */
    public static function setAlternateUrls($routeName, array $args) {

    	$defaultLanguage = I18n::$defaultLanguage;
	    $availableLanguages = I18n::$availableLanguages;

	    foreach ($availableLanguages as $language) {

	    	if($language !== $defaultLanguage) {

			    $args['language'] = $language;
			    self::$alternateUrls[] = [
			    	'language' => $language,
			    	'url' => rf_config('app.url') . rf_link_to($routeName, $args)
			    ];

		    }

	    }

    }

	/**
	 * Force set alternate urls
	 *
	 * @param array $alternateUrls
	 */
    public static function forceSetAlternateUrls(array $alternateUrls) {

        self::$alternateUrls = $alternateUrls;

    }

	/**
	 * Get alternate urls
	 *
	 * @return array
	 */
    public static function getAlternateUrls() {

	    return !empty(self::$alternateUrls) ? self::$alternateUrls : [];

    }

}