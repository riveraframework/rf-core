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
class DatabaseConfiguration extends ServiceConfigurationSet {

    /** @var DatabaseConnectionConfiguration */
    protected $connectionConfiguration;

    /**
     * Get the database type
     *
     * @return string
     */
    public function getType() {

        return $this->get('type');

    }

    /**
     * Get the connection configuration
     *
     * @return DatabaseConnectionConfiguration
     */
    public function getConnectionConfig() {

        if(!isset($this->connectionConfiguration)) {
            $this->connectionConfiguration = new DatabaseConnectionConfiguration($this->get('connection'));
        }

        return $this->connectionConfiguration;

    }

}
