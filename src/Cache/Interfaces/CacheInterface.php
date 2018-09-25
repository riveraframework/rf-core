<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Cache\Interfaces;

/**
 * Interface CacheInterface
 *
 * @package Rf\Core\Cache
 */
interface CacheInterface {

	/**
	 * Get cache identifier
	 *
	 * @return string
	 */
	public function getIdentifier();

	/**
	 * Set cache identifier
	 *
	 * @param string $identifier
	 */
	public function setIdentifier($identifier);

    /**
     * Get cache type
     *
     * @return string
     */
    public function getType();

    /**
     * Get cache endpoints
     *
     * @return string
     */
    public function getEndpoints();

	/**
	 * Get value
	 *
	 * @param string $key
	 *
	 * @return string
	 */
	public function get($key);

	/**
	 * Set value
	 *
	 * @param string $key
	 * @param string $value
	 */
	public function set($key, $value);

	/**
	 * Delete value
	 *
	 * @param string $key
	 */
	public function delete($key);

	/**
	 * Flush cache
	 */
	public function flush();

	/**
	 * Get cache stats
	 */
	public function getStats();

}