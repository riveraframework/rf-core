<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Database\QueryEngine;

use Rf\Core\Database\Query;
use Rf\Core\Database\QueryEngine\Clauses\FromTrait;
use Rf\Core\Database\QueryEngine\Clauses\GroupByTrait;
use Rf\Core\Database\QueryEngine\Clauses\HavingTrait;
use Rf\Core\Database\QueryEngine\Clauses\JoinTrait;
use Rf\Core\Database\QueryEngine\Clauses\LimitTrait;
use Rf\Core\Database\QueryEngine\Clauses\OrderByTrait;
use Rf\Core\Database\QueryEngine\Clauses\QueryTrait;
use Rf\Core\Database\QueryEngine\Clauses\WhereTrait;

/**
 * Class BaseQuery
 *
 * @package Rf\Core\Database\QueryEngine
 *
 * @TODO: UNION
 * @TODO: Query/subquery prepare() with params
 */
class BaseQuery {

    use QueryTrait, FromTrait, JoinTrait, WhereTrait, GroupByTrait, HavingTrait, OrderByTrait, LimitTrait;

    /** @var string Query type */
    const QUERY_TYPE = '';

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
            $this->database(rf_config('database.name'));
        }

        if(isset($tables)) {
            $this->from($tables);
        }

    }
    
    /**
     * Magic method toString
     *
     * @return string
     */
    public function __toString() {

        return $this->compile();

    }

    /**
     * Create a new query object
     *
     * @param null $tables
     * @param null $database
     *
     * @return BaseQuery
     */
    public static function create($tables = null, $database = null) {

        return new static($tables, $database);

    }

}