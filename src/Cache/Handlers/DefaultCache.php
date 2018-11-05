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

use Rf\Core\Cache\Interfaces\CacheInterface;

/**
 * Class DefaultCache
 *
 * @package Rf\Core\Cache\Handlers
 */
abstract class DefaultCache implements CacheInterface {

	/** @var string $identifier */
	protected $identifier;

    /** @var string $type */
    protected $type;

    /** @var array $endpoints */
    protected $endpoints;

	/**
	 * Get cache identifier
	 *
	 * @return string
	 */
	public function getIdentifier() {

		return $this->identifier;

	}

	/**
	 * Set cache identifier
	 *
	 * @param string $identifier
	 */
	public function setIdentifier($identifier) {

		$this->identifier = $identifier;

	}

    /**
     * Get cache type
     *
     * @return string
     */
    public function getType() {

        return $this->type;

    }

    /**
     * Get cache endpoints
     *
     * @return array
     */
    public function getEndpoints() {

        return $this->endpoints;

    }

	/**
	 * Generate a basic cache key
	 *
	 * @param string[] $params
	 *
	 * @return string
	 */
	public static function generateBasicCacheKey(array $params) {

		return md5(implode('-', $params));

	}

	/**
	 * Generate a basic cache key with a prefix
	 *
	 * @param string $prefix
	 * @param string[] $params
	 *
	 * @return string
	 */
	public static function generateBasicCacheKeyWithPrefix($prefix, array $params) {

		return $prefix . md5(implode('-', $params));

	}

}