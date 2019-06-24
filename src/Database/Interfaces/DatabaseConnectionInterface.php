<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Database\Interfaces;

use Rf\Core\Base\Exceptions\DebugException;
use Rf\Core\Database\DatabaseConfiguration;
use Rf\Core\Database\MySQL\QueryBuilder\Query;

/**
 * Class DatabaseConnectionInterface
 *
 * @package Rf\Core\Database\Interfaces
 */
interface DatabaseConnectionInterface {

    /**
     * MySQLConnection constructor.
     *
     * @param DatabaseConfiguration $configuration
     */
    public function __construct(DatabaseConfiguration $configuration);

    /**
     * Get a query builder
     *
     * @return DatabaseQueryBuilderInterface
     */
    public function getQueryBuilder();

    /**
     * Prepare and execute a SQL query
     *
     * @param Query|string $query Query to execute
     * @param $valueArray
     *
     * @return int
     * @throws DebugException
     */
    public function execute($query, $valueArray);

    /**
     * Add a record in database and get the ID
     *
     * @param Query|string $query Query to execute
     * @param array $valueArray
     *
     * @return string
     * @throws DebugException
     */
    public function addAndGetId($query, $valueArray);

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
    public function executeToArray($query, $valueArray = [], $forceArray = false, $mode = 'default');

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
    public function executeToObject($query, $valueArray = [], $className = null, $forceArray = false, array $options = []);

    /**
     * Core process to get the table list to generate entity files
     *
     * @TODO: Add support for multiple databases
     *
     * @param string $databaseName
     * @return array
     *
     * @throws \Exception
     */
    public function getTableList($databaseName);

	/**
	 * Rollback only if in transaction
	 */
	public function rollBack();

}