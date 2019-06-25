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

use Rf\Core\Database\MySQL\QueryBuilder\Actions\ExecuteInsertTrait;
use Rf\Core\Database\MySQL\QueryBuilder\Actions\ExecuteSelectTrait;
use Rf\Core\Database\MySQL\QueryBuilder\Actions\ExecuteTrait;
use Rf\Core\Database\MySQL\QueryBuilder\Actions\GenerateTrait;
use Rf\Core\Database\MySQL\QueryBuilder\Others\QueryConnectionTrait;

/**
 * Class Query
 *
 * @package Rf\Core\Database\MySQL\QueryBuilder
 */
class Query extends BaseQuery {

    use QueryConnectionTrait, GenerateTrait, ExecuteTrait, ExecuteSelectTrait, ExecuteInsertTrait;

    /**
     * Magic method toString
     *
     * @return string
     */
    public function __toString() {

        return $this->compile();

    }

}