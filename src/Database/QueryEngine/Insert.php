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
use Rf\Core\Database\QueryEngine\Actions\ExecuteInsertTrait;

/**
 * Class Insert
 *
 * @package Rf\Core\Database\QueryEngine
 */
class Insert extends Query {

    use ExecuteInsertTrait;

    /**
     * Insert constructor.
     *
     * @param string|array|Query|null $tables
     * @param string|null $database
     */
    public function __construct($tables = null, $database = null) {

        parent::__construct('insert', $tables, $database);

    }

}