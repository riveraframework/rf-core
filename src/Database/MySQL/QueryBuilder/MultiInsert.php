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

/**
 * Class MultiInsert
 *
 * @package Rf\Core\Database\MySQL\QueryBuilder
 */
class MultiInsert extends Query {

    use ExecuteInsertTrait;

    /** @var bool $updateOnDuplicate */
    public $updateOnDuplicate = false;

    /**
     * MultiInsert constructor.
     *
     * @param string|array|Query|null $tables
     * @param string|null $database
     */
    public function __construct($tables = null, $database = null) {

        parent::__construct('multi-insert', $tables, $database);

    }

    /**
     * Update on duplicate
     */
    public function updateOnDuplicate() {

    	$this->updateOnDuplicate = true;

    }

}