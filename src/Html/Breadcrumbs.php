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

use Rf\Core\Base\GlobalSingleton;

/**
 * Class Breadcrumbs
 *
 * @since 1.0
 *
 * @package Rf\Core\Html
 */
class Breadcrumbs extends GlobalSingleton {

    /**
     * @var array $elements
     * @since 1.0
     */
    protected static $elements = array();

    /**
     * @var bool $breadcrumbsDisplay
     * @since 1.0
     */
    protected static $breadcrumbsDisplay = true;

    /**
     * This function allow to hide breadcrumbs for current page
     *
     * @since 1.0
     *
     * @return void
     */
    public static function hideBreadcrumbs() {

        self::$breadcrumbsDisplay = false;

    }

    /**
     * This function allow to force breadcrumbs display for current page
     *
     * @since 1.0
     *
     * @return void
     */
    public static function displayBreadcrumbs() {

        self::$breadcrumbsDisplay = true;

    }

    /**
     * Get the display status for breadcrumbs
     *
     * @since 1.0
     *
     * @return boolean
     */
    public static function breadcrumbsDisplayStatus() {

        return self::$breadcrumbsDisplay;

    }

    /**
     * Add an element to the breadcrumbs
     *
     * @since 1.0
     *
     * @param string $type link|list
     * @param array $value array where name => link or list of arrays
     * @return void
     */
    public function addElement($type, $value) {

        if($type === 'link') {

            $names = array_keys($value);
            $name = array_shift($names);
            $uris = array_values($value);
            $uri = array_shift($uris);

            self::$elements[] = array(
                'type' => 'link',
                'name' => $name,
                'uri' => $uri
            );

        } elseif($type === 'list') {

            self::$elements[] = array(
                'type' => $type,
                'elements' => $value
            );

        } else {

            self::$elements[] = array($type => $value);

        }

    }

    /**
     * Get the breadcrumbs elements
     *
     * @since 1.0
     *
     * @return array
     */
    public function getElements() {

        return self::$elements;

    }

} 