<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Wrappers\InternalServices\Memcached;

/**
 * Class MemcachedWrapper
 *
 * @package Rf\Core\Wrappers\InternalServices\Memcached
 */
class MemcachedWrapper {

    /** @var \Memcached  */
	protected $service;

    /** @var array $options */
    protected $options;

    /** @var array */
	protected $servers = [];

	/**
	 * MemcachedWrapper constructor.
     *
     * @param array $options
	 *
	 * @throws \Exception
	 */
    public function __construct(array $options = []) {

        if(!class_exists('\Memcached')) {
            throw new \Exception('Memcached is not configured on this server.');
        }

    	$this->service = new \Memcached();

        // Set options
        $this->options = $options;

    }

    /**
     * Get the Memcached service
     *
     * @return \Memcached
     */
    public function getService() {

        return $this->service;

    }

    /**
     * Add a server
     *
     * @param string $host
     * @param string $port
     */
    public function addServer($host, $port) {

        $this->getService()->addServer($host, $port);

        $this->servers[] = $host . ':' . $port;

    }

    /**
     * Check if the write/read operations work
     *
     * @throws \Exception
     */
    public function checkService() {

        $check = $this->getService()->get('memcached-check');

        if(!$check) {

            $this->getService()->set('memcached-check', 1, 3600);
            $check = $this->getService()->get('memcached-check');

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

            foreach($this->servers as $index => $server) {

                for($i = 0; $i < $this->options['attempts_max']; $i++) {

                    $value = $this->getService()->getByKey($server, $key);

                    if($value && $this->getService()->getResultCode() === \Memcached::RES_SUCCESS) {

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
                    } elseif(in_array($this->getService()->getResultCode(), [
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

                foreach($this->servers as $index => $server) {

                    // Remove data from server when the replication max is reached
                    if($currentReplicationCount > $maxReplication) {

                        $this->getService()->deleteByKey($server, $key);
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
                    $this->getService()->setByKey($server, $key, $value, $expires);

                    // Increment replication count
                    $currentReplicationCount++;

                }

            }

            return $finalValue;

        } else {

            return $this->getService()->get($key);

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

            foreach($this->servers as $server) {

                if(++$count <= $max) {

                    $this->getService()->setByKey($server, $key, $value, $expires);

                } else {

                    $this->getService()->deleteByKey($server, $key);

                }

            }

        } else {

            $this->getService()->set($key, $value, $expires);

        }

    }

    /**
     * Delete value
     *
     * @param string $key
     */
    public function delete($key) {

        if(!empty($this->options['replication'])) {

            foreach($this->servers as $server) {

                $this->getService()->deleteByKey($server, $key);

            }

        } else {

            $this->getService()->delete($key);

        }

    }

    /**
     * Flush cache
     */
    public function flush() {

        $this->getService()->flush();

    }

    /**
     * Get cache stats
     *
     * @return array
     */
    public function getStats() {

        return $this->getService()->getStats();

    }

}