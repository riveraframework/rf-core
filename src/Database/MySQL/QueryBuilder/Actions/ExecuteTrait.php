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

use Rf\Core\Base\Exceptions\DebugException;
use Rf\Core\Database\MySQL\MySQLConnection;

/**
 * Trait ExecuteTrait
 *
 * @package Rf\Core\Database\MySQL\QueryBuilder\Actions
 */
trait ExecuteTrait {

    /**
     * Prepare and execute a SQL query
     *
     * @return int
     * @throws \Exception
     * @throws DebugException
     */
    public function execute() {


        /** @var MySQLConnection $conn */
        $conn = $this->getConnection();

        return $conn->execute($this->compile(), $this->generateValueArray());

    }

}