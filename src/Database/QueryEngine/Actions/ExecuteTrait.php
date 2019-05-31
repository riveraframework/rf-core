<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Database\QueryEngine\Actions;

/**
 * Trait ExecuteTrait
 *
 * @package Rf\Core\Database\QueryEngine\Actions
 */
trait ExecuteTrait {

    /**
     * Prepare and execute a SQL query
     *
     * @return int
     * @throws \Rf\Core\Exception\DebugException
     */
    public function execute() {

        return $this->getConnection()->execute($this->compile(), $this->generateValueArray());

    }

}