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

use Rf\Core\Database\MySQL\QueryBuilder\Delete;
use Rf\Core\Database\MySQL\QueryBuilder\Describe;
use Rf\Core\Database\MySQL\QueryBuilder\Insert;
use Rf\Core\Database\MySQL\QueryBuilder\MultiInsert;
use Rf\Core\Database\MySQL\QueryBuilder\Query;
use Rf\Core\Database\MySQL\QueryBuilder\Select;
use Rf\Core\Database\MySQL\QueryBuilder\Update;

/**
 * Class DatabaseQueryBuilderInterface
 *
 * @package Rf\Core\Database\Interfaces
 */
interface DatabaseQueryBuilderInterface {

    /**
     * Returns a new query object
     *
     * @param string $type
     * @param string|null $tables
     * @param string|null $database
     *
     * @return Query
     */
    public function query($type, $tables = null, $database = null);

    /**
     * Returns a new select query object
     *
     * @param string|null $tables
     * @param string|null $database
     *
     * @return Select
     */
    public function select($tables = null, $database = null);

    /**
     * Returns a new insert query object
     *
     * @param string|null $tables
     * @param string|null $database
     *
     * @return Insert
     */
    public function insert($tables = null, $database = null);

    /**
     * Returns a new multi-insert query object
     *
     * @param string|null $tables
     * @param string|null $database
     *
     * @return MultiInsert
     */
    public function multiInsert($tables = null, $database = null);

    /**
     * Returns a new update query object
     *
     * @param string|null $tables
     * @param string|null $database
     *
     * @return Update
     */
    public function update($tables = null, $database = null);

    /**
     * Returns a new delete query object
     *
     * @param string|null $tables
     * @param string|null $database
     *
     * @return Delete
     */
    public function delete($tables = null, $database = null);

    /**
     * Returns a new describe query object
     *
     * @param string|null $tables
     * @param string|null $database
     *
     * @return Describe
     */
    public function describe($tables = null, $database = null);

}