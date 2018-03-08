<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Application;

use Rf\Core\Cache\CacheHelpers;
use Rf\Core\System\SystemHelpers;

/**
 * Class Autoload
 *
 * @package Rf\Core\Application
 * @version 1.0
 * @since 1.0
 */
abstract class Autoload
{

    /**
     * Init all autoloaders using the spl_autoload_register function
     */
    public static function init() {

        spl_autoload_register([static::class, 'autoloadAppFiles']);

        // Load helpers
        ApplicationHelpers::init();
        CacheHelpers::init();
        SystemHelpers::init();

    }

    /**
     * Autoloader for application library classes
     *
     * @param string $className Class name to load
     */
    public static function autoloadAppFiles($className) {

        if (strpos($className, 'App\\') === 0) {

            $relative_NS = str_replace('App\\', '\\', $className);
            $classNameParts = explode('\\', $relative_NS);
            foreach ($classNameParts as $key => &$classNamePart) {

                if ($key >= count($classNameParts) - 1) {
                    break;
                }

                $classNamePart = lcfirst($classNamePart);

            }
            $translated_path = implode('/', $classNameParts);
            if (file_exists(rf_dir('app') . '/' . $translated_path . '.php')) {
                require_once rf_dir('app') . '/' . $translated_path . '.php';
                return;
            }

        }

    }

}