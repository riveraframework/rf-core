<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Database\MySQL\QueryBuilder\Clauses;

use Rf\Core\Database\MySQL\QueryBuilder\Query;

/**
 * Class FromTrait
 */
trait FromTrait {

    /**
     * @var string $database Target database name
     */
    public $database;

    /**
     * @var array $tables Table list
     */
    public $tables = [];

    /**
     * Set target database
     *
     * @param string $database Target database name
     */
    public function database($database) {

        $this->database = $database;

    }

    /**
     * Set one or more target tables for the current query
     *
     * @param string|array|Query $from Table list: array('my_table [AS mt]'[, ...])
     */
    public function from($from) {

        if(is_a($from, Query::class)) {

            /** @var Query $from */
            $this->setTable('(' . $from->compile() . ')', 't0');

        } else {

            if(!is_array($from)) {
                $from = explode(',', $from);
            }

            foreach ($from as $table) {

                $tableString = trim($table);
                $tableParts = preg_split('/\sAS\s/i', $tableString);
                $tableName = $tableParts[0];

                if(count($tableParts) === 2) {
                    $tableAlias = $tableParts[1];
                } else {
                    $tableAlias = null;
                }

                $this->setTable($tableName, $tableAlias);

            }

        }

    }

    /**
     * Set/Add a target table for the current query
     *
     * @param string $tableName Table name
     * @param string $tableAlias Table alias
     */
    protected function setTable($tableName, $tableAlias = null) {

        $this->tables[] = [$tableName, $tableAlias];

    }

}