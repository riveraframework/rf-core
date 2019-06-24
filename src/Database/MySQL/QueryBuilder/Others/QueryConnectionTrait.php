<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Database\MySQL\QueryBuilder\Others;

use Rf\Core\Database\MySQL\MySQLConnection;

/**
 * Trait QueryConnectionTrait
 *
 * @package Rf\Core\Database\MySQL\QueryBuilder\Other
 */
trait QueryConnectionTrait {

    /** @var MySQLConnection */
    protected $connection;

    /**
     * Set the connection
     *
     * @param MySQLConnection $connection
     */
    public function setConnection(MySQLConnection $connection) {

        $this->connection = $connection;

    }

    /**
     * Get the query connection
     *
     * @return MySQLConnection
     * @throws \Exception
     */
    public function getConnection() {

        if(!empty($this->connection)) {

            // Return the connection if it's set
            return $this->connection;

        } else {

            // Return the connection from the default database service
            return rf_sp()->getDatabase()->getConnection();

        }

    }

}