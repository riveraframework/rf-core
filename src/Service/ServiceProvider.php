<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Service;

use \Exception;

use Rf\Core\Cache\CacheService;
use Rf\Core\Config\ConfigService;
use Rf\Core\Database\DatabaseService;
use Rf\Core\Debug\DebugService;
use Rf\Core\I18n\I18nService;
use Rf\Core\Log\LogService;
use Rf\Core\Orm\OrmService;
use Rf\Core\Route\RouterService;
use Rf\Core\Session\SessionService;

/**
 * Class ServiceProvider
 *
 * @TODO: Create a loadServices function (like routing) to load registered services from files
 * @TODO: Include the internal services in the service provider (cache, handle request, architect, etc.)
 *
 * @package Rf\Core\Service
 */
class ServiceProvider {

    /** @var ServiceLauncher[] */
    protected $launchers = [];

    /** @var Service[] */
    protected $services = [];

    /** @var array */
    protected $serviceTypes = [];

    /** @var array */
    protected $defaultServicesByTypes = [];

    /**
     * Register a service
     *
     * @param string $type
     * @param string $name
     * @param ServiceLauncher $launcher
     * @param bool $default
     */
    public function add($type, $name, ServiceLauncher $launcher, $default = false) {

        // Register the service type
        $this->addServiceType($type);

        // Add the new service in the service list
        $this->launchers[$name] = $launcher;

        // Set the service as default if applicable
        if($default || empty($this->defaultServicesByTypes[$type])) {

            $this->defaultServicesByTypes[$type] = $name;

        }

    }

    /**
     * Get a service by name
     *
     * @param string $name
     * @param bool $shared
     *
     * @return Service
     * @throws Exception
     */
    public function get($name, $shared = true) {

        if(!$shared) {

            // Create a new service instance if we don't want the shared service
            if(isset($this->launchers[$name])) {

                return $this->launchers[$name]->launch();

            }

        } else {

            // Return the shared service if it is already launched
            if(isset($this->services[$name])) {

                return $this->services[$name];

            }

            // Create a new service instance then returns it if a launcher exist
            if(isset($this->launchers[$name])) {

                $this->services[$name] = $this->launchers[$name]->launch();

                return $this->services[$name];

            }

        }

        throw new Exception('No service found');

    }

    /**
     * Get the default service
     *
     * @param string $type
     * @param bool $shared
     *
     * @return Service
     * @throws Exception
     */
    public function getDefault($type, $shared = true) {

        if(isset($this->defaultServicesByTypes[$type])) {

            $serviceName = $this->defaultServicesByTypes[$type];

            if(!$shared) {

                if(isset($this->launchers[$serviceName])) {

                    return $this->launchers[$serviceName]->launch();

                }

            } elseif(isset($this->services[$serviceName])) {

                return $this->services[$serviceName];

            } elseif(isset($this->launchers[$serviceName])) {

                $this->services[$serviceName] = $this->launchers[$serviceName]->launch();

                return $this->services[$serviceName];

            }

        }

        throw new Exception('No default ' . $type . ' service found');

    }

    /**
     * Add a service type
     *
     * @param string $type
     */
    public function addServiceType($type) {

        if(!in_array($type, $this->serviceTypes)) {

            $this->serviceTypes[] = $type;

        }

    }

    /**
     * Get registered service types
     *
     * @return array
     */
    public function getServiceTypes() {

        return $this->serviceTypes;

    }

    // Internal services

    /**
     * Get a config service
     *
     * @param string $name
     * @param bool $shared
     *
     * @return ConfigService
     * @throws Exception
     */
    public function getConfig($name = '', $shared = true) {

        if($name !== '') {

            $configService = $this->get($name, $shared);

        } else {

            $configService = $this->getDefault(ConfigService::TYPE, $shared);

        }

        /** @var ConfigService $configService */
        return $configService;

    }

    /**
     * Get a router service
     *
     * @param string $name
     * @param bool $shared
     *
     * @return RouterService
     * @throws Exception
     */
    public function getRouter($name = '', $shared = true) {

        if($name !== '') {

            $routerService = $this->get($name, $shared);

        } else {

            $routerService = $this->getDefault(RouterService::TYPE, $shared);

        }

        /** @var RouterService $routerService */
        return $routerService;

    }

    /**
     * Get a database service
     *
     * @param string $name
     * @param bool $shared
     *
     * @return DatabaseService
     * @throws Exception
     */
    public function getDatabase($name = '', $shared = true) {

        if($name !== '') {

            $databaseService = $this->get($name, $shared);

        } else {

            $databaseService = $this->getDefault(DatabaseService::TYPE, $shared);

        }

        /** @var DatabaseService $databaseService */
        return $databaseService;

    }

    /**
     * Get a log service
     *
     * @param string $name
     * @param bool $shared
     *
     * @return LogService
     * @throws Exception
     */
    public function getLog($name = '', $shared = true) {

        if($name !== '') {

            $logService = $this->get($name, $shared);

        } else {

            $logService = $this->getDefault(LogService::TYPE, $shared);

        }

        /** @var LogService $logService */
        return $logService;

    }

    /**
     * Get a debug service
     *
     * @param string $name
     * @param bool $shared
     *
     * @return DebugService
     * @throws Exception
     */
    public function getDebug($name = '', $shared = true) {

        if($name !== '') {

            $debugService = $this->get($name, $shared);

        } else {

            $debugService = $this->getDefault(DebugService::TYPE, $shared);

        }

        /** @var DebugService $debugService */
        return $debugService;

    }

    /**
     * Get a i18n service
     *
     * @param string $name
     * @param bool $shared
     *
     * @return I18nService
     * @throws Exception
     */
    public function getI18n($name = '', $shared = true) {

        if($name !== '') {

            $i18nService = $this->get($name, $shared);

        } else {

            $i18nService = $this->getDefault(I18nService::TYPE, $shared);

        }

        /** @var I18nService $i18nService */
        return $i18nService;

    }

    /**
     * Get a orm service
     *
     * @param string $name
     * @param bool $shared
     *
     * @return OrmService
     * @throws Exception
     */
    public function getOrm($name = '', $shared = true) {

        if($name !== '') {

            $ormService = $this->get($name, $shared);

        } else {

            $ormService = $this->getDefault(OrmService::TYPE, $shared);

        }

        /** @var OrmService $ormService */
        return $ormService;

    }

    /**
     * Get a cache service
     *
     * @param string $name
     * @param bool $shared
     *
     * @return CacheService
     * @throws Exception
     */
    public function getCache($name = '', $shared = true) {

        if($name !== '') {

            $cacheService = $this->get($name, $shared);

        } else {

            $cacheService = $this->getDefault(CacheService::TYPE, $shared);

        }

        /** @var CacheService $cacheService */
        return $cacheService;

    }

    /**
     * Get a session service
     *
     * @param string $name
     * @param bool $shared
     *
     * @return SessionService
     * @throws Exception
     */
    public function getSession($name = '', $shared = true) {

        if($name !== '') {

            $sessionService = $this->get($name, $shared);

        } else {

            $sessionService = $this->getDefault(SessionService::TYPE, $shared);

        }

        /** @var SessionService $sessionService */
        return $sessionService;

    }

}