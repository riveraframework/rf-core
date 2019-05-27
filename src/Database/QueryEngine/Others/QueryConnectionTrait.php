<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Database\QueryEngine\Others;

use Rf\Core\Database\PDO;
use Rf\Core\Database\ConnectionRepository;

/**
 * Trait QueryConnectionTrait
 *
 * @package Rf\Core\Database\QueryEngine\Other
 */
trait QueryConnectionTrait {

    /** @var PDO */
    protected $connection;

    /**
     * Set the connection
     *
     * @param PDO $connection
     */
    public function setConnection(PDO $connection) {

        $this->connection = $connection;

    }

    /**
     * Get the connection
     *
     * @return PDO
     */
    public function getConnection() {

        if(empty($this->connection)) {

            return ConnectionRepository::getDefaultConnection();

        } else {

            return $this->connection;

        }

    }

}