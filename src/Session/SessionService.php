<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Session;

use Rf\Core\Config\Exceptions\ConfigException;
use Rf\Core\Log\LogService;
use Rf\Core\Service\Service;
use Rf\Core\Session\Interfaces\SessionInterface;
use Rf\Core\Session\Sessions\MemcachedSession;
use Rf\Core\Session\Sessions\MemcacheSession;
use Rf\Core\Session\Sessions\PhpSession;

/**
 * Class SessionService
 *
 * @package Rf\Core\Session
 */
class SessionService extends Service {

    /** @var string  */
    const TYPE = 'session';

    const HANDLER_TYPE_PHP = 'php';
    const HANDLER_TYPE_MEMCACHE = 'memcache';
    const HANDLER_TYPE_MEMCACHED = 'memcached';

    /** @var SessionConfiguration */
    protected $configuration;

    /** @var SessionInterface[] $handlers */
    protected $handlers;

    /**
     * Load the cache configuration
     *
     * @param array $configuration
     *
     * @throws ConfigException
     * @throws \Exception
     */
    public function loadConfiguration(array $configuration) {

        $this->configuration = new SessionConfiguration($configuration);

        if(!empty($cacheConfig['handlers'])) {

            foreach ($cacheConfig['handlers'] as $handlerIdentifier => $handlerConfig) {

                // Check if the handler type is authorized
                $handlerType = !empty($handlerConfig['type']) ? $handlerConfig['type'] : '';
                if (!in_array($handlerType, [
                    self::HANDLER_TYPE_PHP,
                    self::HANDLER_TYPE_MEMCACHE,
                    self::HANDLER_TYPE_MEMCACHED,
                ])) {
                    throw new ConfigException(LogService::TYPE_ERROR, 'Cache setup error: the cache type `' . $handlerType . '` does not exists');
                }

                switch ($handlerType) {

                    // Create php session handler
                    case self::HANDLER_TYPE_PHP:

                        $phpSession = new PhpSession($this->getName(), !empty($handlerConfig['options']) ? $handlerConfig['options'] : []);

                        if(!empty($sessionConfig['autostart'])) {
                            $phpSession->start();
                        }

                        $this->handlers[] = $phpSession;
                        break;

                    // Create Memcache handler
                    case self::HANDLER_TYPE_MEMCACHE:
                    case self::HANDLER_TYPE_MEMCACHED:

                        if($handlerType === self::HANDLER_TYPE_MEMCACHE) {
                            $handler = new MemcacheSession($this->getName(), !empty($handlerConfig['options']) ? $handlerConfig['options'] : []);
                        } else {
                            $handler = new MemcachedSession($this->getName(), !empty($handlerConfig['options']) ? $handlerConfig['options'] : []);
                        }

                        // Check if the Memcached server list is empty
                        $servers = $handlerConfig['servers'];
                        if (empty($servers)) {
                            throw new ConfigException(LogService::TYPE_ERROR, 'Cache setup error: the Memcached server list is empty');
                        }

                        // Add listed server to the Memcached handler
                        foreach ($servers as $server) {

                            if (empty($server['host']) || empty($server['port'])) {
                                throw new ConfigException(LogService::TYPE_ERROR, 'Cache setup error: the Memcached configuration is invalid');
                            }

                            $handler->addServer($server['host'], $server['port']);

                        }

                        // Check that the memcached server support the common operations
                        if (!empty($handlerConfig['required'])) {
                            $handler->checkService();
                        }

                        if(!empty($sessionConfig['autostart'])) {
                            $handler->start();
                        }

                        $this->handlers[] = $handler;
                        break;

                }

            }

        }

    }

    /**
     * Get available session handlers
     *
     * @return SessionInterface[]
     */
    public function getHandlers() {

        return $this->handlers;

    }

    /**
     * Get the session cookie config
     * @link: http://php.net/manual/fr/function.session-get-cookie-params.php
     *
     * @param null|string $key
     *
     * @return array|string|null
     */
    public static function getConfig($key = null) {

        $config = session_get_cookie_params();

        if(!isset($key)) {

            return $config;

        }

        if(isset($config[$key])) {

            return $config[$key];

        }

        return null;

    }

}