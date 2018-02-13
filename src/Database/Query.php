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

use Rf\Core\Database\QueryEngine\BaseQuery;
use Rf\Core\Database\QueryEngine\Actions\ExecuteInsertTrait;
use Rf\Core\Database\QueryEngine\Actions\ExecuteSelectTrait;
use Rf\Core\Database\QueryEngine\Actions\ExecuteTrait;
use Rf\Core\Database\QueryEngine\Actions\GenerateTrait;
use Rf\Core\Database\QueryEngine\Others\QueryConnectionTrait;

/**
 * Class Query
 *
 * @package Rf\Core\Database
 */
class Query extends BaseQuery {

    use QueryConnectionTrait, GenerateTrait, ExecuteTrait, ExecuteSelectTrait, ExecuteInsertTrait;

}