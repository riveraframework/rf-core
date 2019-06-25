<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Database\MySQL\QueryBuilder;

use Rf\Core\Database\MySQL\QueryBuilder\Clauses\FromTrait;
use Rf\Core\Database\MySQL\QueryBuilder\Clauses\GroupByTrait;
use Rf\Core\Database\MySQL\QueryBuilder\Clauses\HavingTrait;
use Rf\Core\Database\MySQL\QueryBuilder\Clauses\JoinTrait;
use Rf\Core\Database\MySQL\QueryBuilder\Clauses\LimitTrait;
use Rf\Core\Database\MySQL\QueryBuilder\Clauses\OrderByTrait;
use Rf\Core\Database\MySQL\QueryBuilder\Clauses\QueryTrait;
use Rf\Core\Database\MySQL\QueryBuilder\Clauses\WhereTrait;

/**
 * Class BaseQuery
 *
 * @package Rf\Core\Database\MySQL\QueryBuilder
 *
 * @TODO: UNION
 * @TODO: Query/subquery prepare() with params
 */
abstract class BaseQuery {

    use QueryTrait, FromTrait, JoinTrait, WhereTrait, GroupByTrait, HavingTrait, OrderByTrait, LimitTrait;

    /**
     * Create a new Query
     *
     * @param string $type ( select | insert | update | delete | describe )
     * @param string|array|Query|null $tables
     * @param string|null $database
     */
    public function __construct($type, $tables = null, $database = null) {

        $this->setType($type);

        if(isset($database)) {
            $this->database($database);
        } else {
            $this->database(rf_sp()->getDatabase()->getConfiguration()->getConnectionConfig()->getDatabaseName());
        }

        if(isset($tables)) {
            $this->from($tables);
        }

    }

}