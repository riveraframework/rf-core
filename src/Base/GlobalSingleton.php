<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Base;

use Rf\Core\Application\Application;

/**
 * Class GlobalSingleton
 * The GlobalSingleton when extended provide a support in order to keep instances
 * accessible from anywhere inside the app
 *
 * @package Rf\Core\Base
 * @version 1.0
 * @since 1.0
 */
abstract class GlobalSingleton {

    /** @var array Contain all available singleton instances */
    private static $instances = [];

    /**
     * Protected default constructor
     */
    protected function __construct() { }

    /**
     * Clone magic method
     */
    final private function __clone() { }

    /**
     * Get the current instance of the class
     *
     * @TODO: Refactor?
     *
     * @return static
     */
    final public static function getInstance() {

        $calledClass = get_called_class();

        if (!isset(self::$instances[$calledClass])) {
            
            // Patch for class Application when extended
            // @TODO: Remove if block
            if($calledClass === Application::class) {

                foreach (self::$instances as $instance) {

                    if(is_subclass_of($instance, Application::class)) {
                        return $instance;
                    }

                }

            }

            self::$instances[$calledClass] = new $calledClass();

        }

        return self::$instances[$calledClass];

    }

}