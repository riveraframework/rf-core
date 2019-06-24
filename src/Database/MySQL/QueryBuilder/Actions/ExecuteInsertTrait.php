<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Database\MySQL\QueryBuilder\Actions;

use Rf\Core\Database\MySQL\MySQLConnection;

/**
 * Trait ExecuteInsertTrait
 *
 * @package Rf\Core\Database\MySQL\QueryBuilder\Actions
 */
trait ExecuteInsertTrait {

    /**
     * Add a record in database and get the ID
     *
     * @return string
     * @throws \Exception
     */
    public function addAndGetId() {

        /** @var MySQLConnection $conn */
        $conn = $this->getConnection();

        return $conn->addAndGetId($this->compile(), $this->generateValueArray());

    }

}