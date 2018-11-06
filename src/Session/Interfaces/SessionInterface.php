<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Session\Interfaces;

/**
 * Class SessionInterface
 *
 * @package Rf\Core\Session\Interfaces
 */
interface SessionInterface {

    /**
     * Session constructor.
     *
     * @param string $sessionName
     * @param array $options
     */
    public function __construct($sessionName, $options = []);

    /**
     * Get session name
     *
     * @return string
     */
    public function getName();

    /**
     * Get session ID
     *
     * @return string
     */
    public function getId();

    /**
     * Start session
     */
    public function start();

    /**
     * Stop session
     */
    public function stop();

    /**
     * Get session item
     *
     * @param string $key
     *
     * @return mixed|null|string
     */
    public function get($key);

    /**
     * Set an item in the session
     *
     * @param string $key
     * @param mixed $value
     * @param int $expiration
     */
    public function set($key, $value, $expiration);

    /**
     * Delete an item from the session
     *
     * @param string $key
     */
    public function delete($key);

    /**
     * Destroy the session
     */
    public function destroy();

}