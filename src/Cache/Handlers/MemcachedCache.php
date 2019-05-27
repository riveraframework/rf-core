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

/**
 * Class MemcachedCache
 *
 * @package Rf\Core\Cache\Handlers
 */
class MemcachedCache extends DefaultCache {

    /** @var string $type */
    protected $type = 'memcached';

    /** @var \Memcached $memcached */
    protected $memcached;

    /** @var array $options */
    protected $options;

    /**
     * MemcacheCache constructor.
     */
    public function __construct(array $options = []) {

        if(!class_exists('\Memcached')) {
            throw new \Exception('Memcached is not configured on this server.');
        }

        $this->memcached = new \Memcached();

        // Set options
        $this->options = $options;

    }

    /**
     * Get memcached
     *
     * @return \Memcached
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

        $this->memcached->addServer($host, $port);
        $this->endpoints[] = $host . ':' . $port;

    }

    /**
     * Check if the write/read operations work
     *
     * @throws \Exception
     */
    public function checkService() {

        $check = $this->memcached->get('memcached-check');

        if(!$check) {

            $this->memcached->set('memcached-check', 1, 3600);
            $check = $this->memcached->get('memcached-check');

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

                    $value = $this->memcached->getByKey($server, $key);

                    if($value && $this->memcached->getResultCode() === \Memcached::RES_SUCCESS) {

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
                    } elseif(in_array($this->memcached->getResultCode(), [
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

                        $this->memcached->deleteByKey($server, $key);
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
                    $this->memcached->setByKey($server, $key, $value, $expires);

                    // Increment replication count
                    $currentReplicationCount++;

                }

            }

            return $finalValue;

        } else {

            return $this->memcached->get($key);

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

                    $this->memcached->setByKey($server, $key, $value, $expires);

                } else {

                    $this->memcached->deleteByKey($server, $key);

                }

            }

        } else {

            $this->memcached->set($key, $value, $expires);

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

                $this->memcached->deleteByKey($server, $key);

            }

        } else {

            $this->memcached->delete($key);

        }

    }

    /**
     * Flush cache
     */
    public function flush() {

        $this->memcached->flush();

    }

    /**
     * Get cache stats
     *
     * @return array
     */
    public function getStats() {

        return $this->memcached->getStats();

    }

}