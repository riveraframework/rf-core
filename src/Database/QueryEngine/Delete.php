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

/**
 * Class Delete
 *
 * @package Rf\Core\Database\QueryEngine
 */
class Delete extends Query {

    public function __construct($tables = null, $database = null) {

        parent::__construct('delete', $tables, $database);

    }
}