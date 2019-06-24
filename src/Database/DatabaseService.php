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

use Rf\Core\Database\Interfaces\DatabaseConnectionInterface;
use Rf\Core\Database\Interfaces\DatabaseServiceInterface;
use Rf\Core\Database\MySQL\MySQLConnection;
use Rf\Core\Service\Service;

/**
 * Class DatabaseService
 *
 * @package Rf\Core\Database
 */
class DatabaseService extends Service implements DatabaseServiceInterface {

    /** @var string  */
    const TYPE = 'database';

    /** @var string  */
    const DATABASE_TYPE_MYSQL = 'mysql';

    /** @var string Index type */
    const OPTION_INDEX_BY_ID = 'index-by-id';

    /** @var DatabaseConfiguration */
    protected $configuration;

    /** @var DatabaseConnectionInterface */
    public $connection;

    /**
     * {@inheritDoc}
     */
    public function loadConfiguration(array $configuration) {

        $this->configuration = new DatabaseConfiguration($configuration);

    }

    /**
     * {@inheritDoc}
     *
     * @return DatabaseConfiguration
     */
    public function getConfiguration() {

        return $this->configuration;

    }

    /**
     * {@inheritDoc}
     */
    public function getConnection() {

        if(!isset($this->connection)) {

            switch ($this->getConfiguration()->getType()) {

                case self::DATABASE_TYPE_MYSQL:
                default:
                    $this->connection = new MySQLConnection($this->getConfiguration()->getConnectionConfig());
                    break;

            }

        }

        return $this->connection;

    }

}