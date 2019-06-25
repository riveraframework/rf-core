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

use Rf\Core\Cache\CacheService;
use Rf\Core\Config\ConfigService;
use Rf\Core\Debug\DebugService;
use Rf\Core\I18n\I18nService;
use Rf\Core\Log\LogService;
use Rf\Core\Orm\OrmService;
use Rf\Core\Route\RouterService;
use Rf\Core\Session\SessionService;

/**
 * Class ServiceLauncherFactory
 *
 * @package Rf\Core\Service
 */
abstract class ServiceLauncherFactory {

    /**
     * Create a config service launcher
     *
     * @param string $type
     * @param string $name
     * @param array $configuration
     * @param bool $default
     *
     * @return ServiceLauncher
     */
    public static function createConfigServiceLauncher($type, $name, $configuration, $default) {

        $launcher = new ServiceLauncher(function () use ($type, $name, $configuration, $default) {

            return new ConfigService($type, $name, $configuration, $default);

        });

        return $launcher;

    }

    /**
     * Create a cache service launcher
     *
     * @param string $type
     * @param string $name
     * @param array $configuration
     * @param bool $default
     *
     * @return ServiceLauncher
     */
    public static function createCacheServiceLauncher($type, $name, $configuration, $default) {

        $launcher = new ServiceLauncher(function () use ($type, $name, $configuration, $default) {

            return new CacheService($type, $name, $configuration, $default);

        });

        return $launcher;

    }

    /**
     * Create a debug service launcher
     *
     * @param string $type
     * @param string $name
     * @param array $configuration
     * @param bool $default
     *
     * @return ServiceLauncher
     */
    public static function createDebugServiceLauncher($type, $name, $configuration, $default) {

        $launcher = new ServiceLauncher(function () use ($type, $name, $configuration, $default) {

            return new DebugService($type, $name, $configuration, $default);

        });

        return $launcher;

    }

    /**
     * Create a i18n service launcher
     *
     * @param string $type
     * @param string $name
     * @param array $configuration
     * @param bool $default
     *
     * @return ServiceLauncher
     */
    public static function createI18nServiceLauncher($type, $name, $configuration, $default) {

        $launcher = new ServiceLauncher(function () use ($type, $name, $configuration, $default) {

            return new I18nService($type, $name, $configuration, $default);

        });

        return $launcher;

    }

    /**
     * Create a log service launcher
     *
     * @param string $type
     * @param string $name
     * @param array $configuration
     * @param bool $default
     *
     * @return ServiceLauncher
     */
    public static function createLogServiceLauncher($type, $name, $configuration, $default) {

        $launcher = new ServiceLauncher(function () use ($type, $name, $configuration, $default) {

            return new LogService($type, $name, $configuration, $default);

        });

        return $launcher;

    }

    /**
     * Create a orm service launcher
     *
     * @param string $type
     * @param string $name
     * @param array $configuration
     * @param bool $default
     *
     * @return ServiceLauncher
     */
    public static function createOrmServiceLauncher($type, $name, $configuration, $default) {

        $launcher = new ServiceLauncher(function () use ($type, $name, $configuration, $default) {

            return new OrmService($type, $name, $configuration, $default);

        });

        return $launcher;

    }

    /**
     * Create a router service launcher
     *
     * @param string $type
     * @param string $name
     * @param array $configuration
     * @param bool $default
     *
     * @return ServiceLauncher
     */
    public static function createRouterServiceLauncher($type, $name, $configuration, $default) {

        $launcher = new ServiceLauncher(function () use ($type, $name, $configuration, $default) {

            return new RouterService($type, $name, $configuration, $default);

        });

        return $launcher;

    }

    /**
     * Create a session service launcher
     *
     * @param string $type
     * @param string $name
     * @param array $configuration
     * @param bool $default
     *
     * @return ServiceLauncher
     */
    public static function createSessionServiceLauncher($type, $name, $configuration, $default) {

        $launcher = new ServiceLauncher(function () use ($type, $name, $configuration, $default) {

            return new SessionService($type, $name, $configuration, $default);

        });

        return $launcher;

    }

    /**
     * Create a custom service launcher
     *
     * @param string $type
     * @param string $name
     * @param array $configuration
     * @param bool $default
     *
     * @return ServiceLauncher
     */
    public static function createCustomServiceLauncher($type, $realType, $name, $configuration, $default) {

        $launcher = new ServiceLauncher(function () use ($type, $realType, $name, $configuration, $default) {

            return new $type($realType, $name, $configuration, $default);

        });

        return $launcher;

    }

}