<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Database\MySQL;

use Rf\Core\Base\Exceptions\DebugException;
use Rf\Core\Database\DatabaseConnectionConfiguration;
use Rf\Core\Database\DatabaseService;
use Rf\Core\Database\MySQL\QueryBuilder\Query;
use Rf\Core\Log\LogService;
use Rf\Core\Orm\Entity;

/**
 * Class MySQLConnection
 *
 * @package Rf\Core\Database
 */
class MySQLConnection extends \PDO {

    use MySQLTools;

    /** @var DatabaseConnectionConfiguration */
    protected $configuration;

    /** @var string */
    protected $currentDatabase;

    /**
     * MySQLConnection constructor.
     *
     * @param DatabaseConnectionConfiguration $configuration
     */
    public function __construct(DatabaseConnectionConfiguration $configuration) {

        // Set the connection configuration
        $this->configuration = $configuration;

        // Set the default database name
        $this->configuration = $configuration->getDatabaseName();

        // Get connection information from the config
        $dsn = 'mysql:host=' . $configuration->getHost() . ';dbname=' . $configuration->getDatabaseName();
        $user = $configuration->getUser();
        $password = $configuration->getPassword();
        $options = [];

        // Get options
        $defaultCharset = $configuration->getDefaultCharset();

        // Process options
        if(!empty($defaultCharset)) {
            $options[MySQLConnection::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES ' . $defaultCharset;
        }

        // Create the connection
        parent::__construct($dsn, $user, $password, $options);

    }

    /**
     * Get a MySQL query builder
     *
     * @return MySQLQueryBuilder
     */
    public function getQueryBuilder() {

        return new MySQLQueryBuilder();

    }

    /**
     * Get the current database to use
     *
     * @return string
     */
    public function getCurrentDatabase() {

        return $this->currentDatabase;

    }

    /**
     * Temporary use a specific database
     * Notes:
     * - The database needs to be accessible through the current connection
     * - This property is reset after any query
     *
     * @param $databaseName
     */
    public function useDatabase($databaseName) {

        $this->currentDatabase = $databaseName;

    }

    /**
     * Reset the custom database name
     */
    public function resetCurrentDatabase() {

        $this->currentDatabase = $this->configuration->getDatabaseName();

    }

    /**
     * Prepare and execute a SQL query
     *
     * @param Query|string $query Query to execute
     * @param $valueArray
     *
     * @return int
     * @throws DebugException
     */
    public function execute($query, $valueArray) {

        // Convert query to string if necessary
        if(is_a($query, Query::class)) {
            $query = $query->compile();
        }

        // Prepare the query
        $prep = $this->prepare($query);

        // Execute the query
        $execute = $prep->execute($valueArray);

        // Reset the database name
        $this->resetCurrentDatabase();

        // Return the result
        if(!$execute) {

            throw new DebugException(LogService::TYPE_ERROR, 'The query couldn\'t be executed, reason: "' . $prep->errorInfo()[2] . '" (' . $query . ' - array(' . implode(', ', $valueArray) . '))');

        } else {

            return $prep->rowCount();

        }

    }

    /**
     * Add a record in database and get the ID
     *
     * @param Query|string $query Query to execute
     * @param array $valueArray
     *
     * @return string
     * @throws DebugException
     */
    public function addAndGetId($query, $valueArray) {

        // Prepare the query
        $prep = $this->prepare($query);

        // Execute the query
        $isAdded = $prep->execute($valueArray);

        // Reset the database name
        $this->resetCurrentDatabase();

        if($isAdded === false) {

            throw new DebugException(LogService::TYPE_ERROR, 'The query couldn\'t be executed, reason: "' . $prep->errorInfo()[2] . '" (' . $query . ' - array(' . implode(', ', $valueArray) . '))');

        } else {

            return $this->lastInsertId();

        }

    }

    /**
     * Get the result of a query as an array or array of arrays
     *
     * @param Query|string $query Query to execute
     * @param array $valueArray Array of the value to use when preparing the query
     * @param bool $forceArray Force the result if unique as an array of array (false by default)
     * @param string $mode assoc|num|default(both)
     *
     * @return array|array[array]
     * @throws DebugException
     */
    public function executeToArray($query, $valueArray = [], $forceArray = false, $mode = 'default') {

        // Prepare the query
        $prep = $this->prepare($query);

        // Execute the query
        $isExecuted = $prep->execute($valueArray);

        // Reset the database name
        $this->resetCurrentDatabase();

        if($isExecuted === false) {

            throw new DebugException(LogService::TYPE_ERROR, 'The query couldn\'t be executed, reason: "' . $prep->errorInfo()[2] . '" (' . $query . ' - array(' . implode(', ', $valueArray) . '))');

        } else {

            // Set fetch mode
            if($mode == 'assoc') {
                $fetch = MySQLConnection::FETCH_ASSOC;
            } elseif($mode == 'num') {
                $fetch = MySQLConnection::FETCH_NUM;
            } else {
                $fetch = MySQLConnection::FETCH_BOTH;
            }

            // Fetch result(s)
            $array = [];
            while($row = $prep->fetch($fetch)) {
                array_push($array, $row);
            }

            // Return the result
            if(!$forceArray && count($array) === 1) {
                return array_shift($array);
            } else {
                return $array;
            }
        }
    }

    /**
     * Get the result of a query as an object or array of objects. A class name can be specified
     * to map the result in an object of this class.
     *
     * @param Query|string $query Query to execute
     * @param array $valueArray Array of the value to use when preparing the query
     * @param string $className Class name to map the properties
     * @param bool $forceArray Force the result if unique as an array of array (false by default)
     * @param array $options Options
     *
     * @return object|object[]
     * @throws \Exception
     * @throws DebugException
     */
    public function executeToObject($query, $valueArray = [], $className = null, $forceArray = false, array $options = []) {

        // Prepare the query
        $prep = $this->prepare($query);

        // Execute the query
        $isExecuted = $prep->execute($valueArray);

        // Reset the database name
        $this->resetCurrentDatabase();

        if($isExecuted === false) {

            throw new DebugException(LogService::TYPE_ERROR, 'The query couldn\'t be executed, reason: "' . $prep->errorInfo()[2] . '" (' . $query . ' - array(' . implode(', ', $valueArray) . '))');

        } else {

            // Set fetch mode
            if(isset($className)) {
                $prep->setFetchMode(MySQLConnection::FETCH_CLASS|MySQLConnection::FETCH_PROPS_LATE, $className);
            } else {
                $prep->setFetchMode(MySQLConnection::FETCH_OBJ);
            }

            // Fetch result(s)
            $objects = [];
            while($row = $prep->fetch()) {

                /** @var \stdClass|Entity $row */

                // Create backup for entities
                if(is_a($row, Entity::class)) {
                    $row->createBackup();
                }

                // Custom indexing of the results
                if(in_array(DatabaseService::OPTION_INDEX_BY_ID, $options)) {

                    if(method_exists($row, 'getId')) {
                        $objects[$row->getId()] = $row;
                    } else {
                        throw new DebugException(LogService::TYPE_ERROR, 'Unable to index by id, the object is missing getId method');
                    }

                } else {
                    array_push($objects, $row);
                }

            }

            // Return the result
            if(count($objects) == 1 && $forceArray === false) {
                return $objects[0];
            } else {
                return $objects;
            }
        }
    }

	/**
	 * Rollback only if in transaction
	 */
	public function rollBack() {

		if($this->inTransaction()) {
			parent::rollBack();
		}

	}

}