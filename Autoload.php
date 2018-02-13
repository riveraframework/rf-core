<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace {

    // Automatically load the vendors added using composer before any of the Rf components
    $vendorAutoloadFile = dirname(dirname(__DIR__)) . '/autoload.php';

    if(file_exists($vendorAutoloadFile)) {
        require_once $vendorAutoloadFile;
    }

}

namespace Rf\Core {

    use Rf\Core\Application\ApplicationDirectories;
    use Rf\Core\Application\ApplicationHelpers;
    use Rf\Core\System\SystemHelpers;

    /**
     * Class Autoload
     *
     * @package Rf\Core
     * @version 1.0
     * @since 1.0
     */
    class Autoload
    {

        /**
         * @var ApplicationDirectories $directories
         */
        protected $directories;

        /**
         * Init all autoloaders using the spl_autoload_register function
         */
        public function __construct() {

            require_once 'src/Application/ApplicationDirectories.php';
            $this->directories = new ApplicationDirectories();

            spl_autoload_register([$this, 'autoloadFrameworkFiles']);
            spl_autoload_register([$this, 'autoloadAppFiles']);

            // Load helpers
            ApplicationHelpers::init();
            SystemHelpers::init();

        }

        /**
         * Get directories
         *
         * @return ApplicationDirectories
         */
        public function getDirectories() {

            return $this->directories;

        }

        /**
         * Autoloader for Rf classes and plugins
         *
         * @param string $className Class name to load
         */
        private function autoloadFrameworkFiles($className) {

            // Test to determine whether if it is a core class or a plugin
            if (strpos($className, 'Rf\\Plugins\\') === 0) {

                $relative_NS = str_replace('Rf\\Plugins\\', '\\', $className);
                $translated_path = str_replace('\\', '/', $relative_NS);

                if (file_exists(dirname(__DIR__) . '/riveraframeworkplugins/' . $translated_path . '.php')) {
                    require_once dirname(__DIR__) . '/riveraframeworkplugins/' . $translated_path . '.php';
                    return;
                }

            } elseif (strpos($className, 'Rf\\Core\\') === 0) {

                $relative_NS = str_replace('Rf\\Core\\', '\\', $className);
                $translated_path = str_replace('\\', '/', $relative_NS);

                if (file_exists(__DIR__ . '/src/' . $translated_path . '.php')) {
                    require_once __DIR__ . '/src/' . $translated_path . '.php';
                    return;
                }

            }

        }

        /**
         * Autoloader for application library classes
         *
         * @param string $className Class name to load
         */
        private function autoloadAppFiles($className) {

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
                if (file_exists($this->directories->get('app') . '/' . $translated_path . '.php')) {
                    require_once $this->directories->get('app') . '/' . $translated_path . '.php';
                    return;
                }

            }

        }

    }

}