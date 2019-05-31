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

use Rf\Core\Base\Exceptions\DebugException;
use Rf\Core\Database\QueryEngine\Select;
use Rf\Core\Log\Log;
use Rf\Core\Orm\Entity;

/**
 * Class PDO
 *
 * @package Rf\Core\Database
 */
class PDO extends \PDO {

    /** @var string */
    protected $name;

    /** @var string */
    protected $currentUser;

    /** @var string */
    protected $currentDatabase;

    /**
     * PDO constructor.
     *
     * @param array $connConfig
     * @param array $connConfig
     */
    public function __construct($name, array $config) {

        $this->name = $name;
        $this->currentUser = $config['user'];
        $this->currentDatabase = $config['name'];

        parent::__construct('mysql:host=' . $config['host'] . ';dbname=' . $config['name'],
            $config['user'],
            $config['password'],
            [
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . $config['charset'],
            ]
        );

    }

    /**
     * Get connection name
     *
     * @return string
     */
    public function getName() {

        return $this->name;

    }

    /**
     * Get current user name
     *
     * @return string
     */
    public function getCurrentUser() {

        return $this->currentUser;

    }

    /**
     * Get current database name
     *
     * @return string
     */
    public function getCurrentDatabase() {

        return $this->currentDatabase;

    }

    /**
     * Prepare and execute a SQL query
     *
     * @param \Rf\Core\Database\Query|string $query Query to execute
     * @param $valueArray
     *
     * @return int
     * @throws DebugException
     */
    public function execute($query, $valueArray) {

        // Prepare the query
        $prep = $this->prepare($query);

        // Execute the query
        $execute = $prep->execute($valueArray);

        // Return the result
        if(!$execute) {

            throw new DebugException(Log::TYPE_ERROR, 'The query couldn\'t be executed, reason: "' . $prep->errorInfo()[2] . '" (' . $query . ' - array(' . implode(', ', $valueArray) . '))');

        } else {
            return $prep->rowCount();
        }

    }

    /**
     * Add a record in database and get the ID
     *
     * @param \Rf\Core\Database\Query|string $query Query to execute
     * @param array $valueArray
     *
     * @return string
     * @throws DebugException
     */
    public function addAndGetId($query, $valueArray) {

        // Prepare the query
        $prep = $this->prepare($query);

        // Execute the query
        if($prep->execute($valueArray) === false) {

            throw new DebugException(Log::TYPE_ERROR, 'The query couldn\'t be executed, reason: "' . $prep->errorInfo()[2] . '" (' . $query . ' - array(' . implode(', ', $valueArray) . '))');

        } else {
            return $this->lastInsertId();
        }
    }

    /**
     * Get the result of a query as an array or array of arrays
     *
     * @param \Rf\Core\Database\Query|string $query Query to execute
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
        if($prep->execute($valueArray) === false) {

            throw new DebugException(Log::TYPE_ERROR, 'The query couldn\'t be executed, reason: "' . $prep->errorInfo()[2] . '" (' . $query . ' - array(' . implode(', ', $valueArray) . '))');

        } else {

            // Set fetch mode
            if($mode == 'assoc') {
                $fetch = PDO::FETCH_ASSOC;
            } elseif($mode == 'num') {
                $fetch = PDO::FETCH_NUM;
            } else {
                $fetch = PDO::FETCH_BOTH;
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
     * @param \Rf\Core\Database\Query|string $query Query to execute
     * @param array $valueArray Array of the value to use when preparing the query
     * @param string $className Class name to map the properties
     * @param bool $forceArray Force the result if unique as an array of array (false by default)
     * @param array $options Options
     *
     * @return object|object[]
     * @throws DebugException
     */
    public function executeToObject($query, $valueArray = [], $className = null, $forceArray = false, array $options = []) {

        // Prepare the query
        $prep = $this->prepare($query);

        if($prep->execute($valueArray) === false) {

            throw new DebugException(Log::TYPE_ERROR, 'The query couldn\'t be executed, reason: "' . $prep->errorInfo()[2] . '" (' . $query . ' - array(' . implode(', ', $valueArray) . '))');

        } else {

            // Set fetch mode
            if(isset($className)) {
                $prep->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, $className);
            } else {
                $prep->setFetchMode(PDO::FETCH_OBJ);
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
                if(in_array(ConnectionRepository::OPTION_INDEX_BY_ID, $options)) {

                    if(method_exists($row, 'getId')) {
                        $objects[$row->getId()] = $row;
                    } else {
                        throw new DebugException(Log::TYPE_ERROR, 'Unable to index by id, the object is missing getId method');
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
     * Core process to get the table list to generate entity files
     *
     * @throws DebugException
     *
     * @TODO: Add support for multiple databases
     */
    public function getTableList($databaseName) {

        $query = new Select('TABLES', 'information_schema');
        $query->fields(['TABLE_NAME', 'TABLE_SCHEMA']);
        $query->whereIn('TABLE_SCHEMA', [$databaseName]);
        $query->whereAnd();
        $query->whereNotLike('TABLE_NAME', 'view_%');

        return $query->toArrayAssoc(true);

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