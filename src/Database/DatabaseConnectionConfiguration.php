<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Database;

use Rf\Core\Service\ServiceConfigurationSet;

/**
 * Class DatabaseConfiguration
 *
 * @package Rf\Core\Database
 */
class DatabaseConnectionConfiguration extends ServiceConfigurationSet {

    /**
     * Get the database type
     *
     * @return string
     */
    public function isEnabled() {

        return $this->get('type');

    }

    /**
     * Get the database host
     *
     * @return string
     */
    public function getConnexionConfig() {

        return $this->get('connection.host');

    }

    /**
     * Get the database host
     *
     * @return string
     */
    public function getHost() {

        return $this->get('connection.host');

    }

    /**
     * Get the database user
     *
     * @return string
     */
    public function getUser() {

        return $this->get('connection.user');

    }

    /**
     * Get the database password
     *
     * @return string
     */
    public function getPassword() {

        return $this->get('connection.password');

    }

    /**
     * Get the database name
     *
     * @return string
     */
    public function getDatabaseName() {

        return $this->get('connection.dbname');

    }

    /**
     * Get the database port
     *
     * @return string
     */
    public function getPort() {

        return $this->get('connection.port');

    }

    /**
     * Get the database default charset
     *
     * @return string
     */
    public function getDefaultCharset() {

        return $this->get('connection.options.default_charset');

    }

}
