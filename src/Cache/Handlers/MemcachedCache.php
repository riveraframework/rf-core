<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Cache\Handlers;

use Rf\Core\Wrappers\InternalServices\Memcached\MemcachedWrapper;

/**
 * Class MemcachedCache
 *
 * @package Rf\Core\Cache\Handlers
 */
class MemcachedCache extends DefaultCache {

    /** @var string $type */
    protected $type = 'memcached';

    /** @var MemcachedWrapper $memcached */
    protected $memcached;

    /** @var array $options */
    protected $options;

    /**
     * MemcacheCache constructor.
     *
     * @param array $options
     *
     * @throws \Exception
     */
    public function __construct(array $options = []) {

        $this->memcached = new MemcachedWrapper();

        // Set options
        $this->options = $options;

    }

    /**
     * Get memcached
     *
     * @return MemcachedWrapper
     */
    public function getMemcached() {

        return $this->memcached;

    }

    /**
     * Add a server
     *
     * @param string $host
     * @param string $port
     */
    public function addServer($host, $port) {

        $this->memcached->getService()->addServer($host, $port);
        $this->endpoints[] = $host . ':' . $port;

    }

    /**
     * Check if the write/read operations work
     *
     * @throws \Exception
     */
    public function checkService() {

        $check = $this->memcached->getService()->get('memcached-check');

        if(!$check) {

            $this->memcached->getService()->set('memcached-check', 1, 3600);
            $check = $this->memcached->getService()->get('memcached-check');

        }

        if(!$check) {
            throw new \Exception('The Memcached servers are not accessible.');
        }

    }

    /**
     * Get value
     *
     * @TODO: Better/different write strategies
     *
     * @param string $key
     * @param int $expires
     *
     * @return string|false
     */
    public function get($key, $expires = 0) {

        if(!empty($this->options['replication'])) {

            $activeServerCount = 0;
            $activeServerIndexes = [];
            $activeReplicationCount = 0;
            $activeReplicationIndexes = [];
            $finalValue = false;

            foreach($this->endpoints as $index => $server) {

                for($i = 0; $i < $this->options['attempts_max']; $i++) {

                    $value = $this->memcached->getService()->getByKey($server, $key);

                    if($value && $this->memcached->getService()->getResultCode() === \Memcached::RES_SUCCESS) {

                        if(!in_array($index, $activeServerIndexes)) {
                            $activeServerCount++;
                            $activeServerIndexes[] = $index;
                        }

                        if(!in_array($index, $activeReplicationIndexes)) {
                            $activeReplicationCount++;
                            $activeReplicationIndexes[] = $index;
                        }

                        $finalValue = $value;

                        break;

                        // @TODO: Improve errors codes
                    } elseif(in_array($this->memcached->getService()->getResultCode(), [
                        \Memcached::RES_NOTFOUND,
                        \Memcached::RES_DELETED,
                        \Memcached::RES_NOTSTORED,
                    ])) {

                        if(!in_array($index, $activeServerIndexes)) {
                            $activeServerCount++;
                            $activeServerIndexes[] = $index;
                        }

                    }

                }

            }

            // @TODO: Do this process in background
            if(
                $this->options['replicate_to'] > $activeReplicationCount
            ) {

                $currentReplicationCount = $activeReplicationCount;
                $maxReplication = min($this->options['replicate_to'], $activeServerCount);

                foreach($this->endpoints as $index => $server) {

                    // Remove data from server when the replication max is reached
                    if($currentReplicationCount > $maxReplication) {

                        $this->memcached->getService()->deleteByKey($server, $key);
                        continue;

                    }

                    // Skip if data already present
                    if(in_array($index, $activeReplicationIndexes)) {
                        continue;
                    }

                    // Skip if server not reachable
                    if(!in_array($index, $activeServerIndexes)) {
                        continue;
                    }

                    // Add the replication
                    // @TODO: Handle proper expiration
                    $this->memcached->getService()->setByKey($server, $key, $value, $expires);

                    // Increment replication count
                    $currentReplicationCount++;

                }

            }

            return $finalValue;

        } else {

            return $this->memcached->getService()->get($key);

        }

    }

    /**
     * Set value
     *
     * @TODO: Better/different write strategies
     *
     * @param string $key
     * @param string $value
     * @param int $expires
     */
    public function set($key, $value, $expires = 0) {

        if(!empty($this->options['replication'])) {

            $count = 0;
            $max = $this->options['replicate_to'];

            foreach($this->endpoints as $server) {

                if(++$count <= $max) {

                    $this->memcached->getService()->setByKey($server, $key, $value, $expires);

                } else {

                    $this->memcached->getService()->deleteByKey($server, $key);

                }

            }

        } else {

            $this->memcached->getService()->set($key, $value, $expires);

        }

    }

    /**
     * Delete value
     *
     * @param string $key
     */
    public function delete($key) {

        if(!empty($this->options['replication'])) {

            foreach($this->endpoints as $server) {

                $this->memcached->getService()->deleteByKey($server, $key);

            }

        } else {

            $this->memcached->getService()->delete($key);

        }

    }

    /**
     * Flush cache
     */
    public function flush() {

        $this->memcached->getService()->flush();

    }

    /**
     * Get cache stats
     *
     * @return array
     */
    public function getStats() {

        return $this->memcached->getService()->getStats();

    }

}