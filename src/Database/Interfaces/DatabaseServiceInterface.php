<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Database\Interfaces;

use Rf\Core\Database\DatabaseConfiguration;
use Rf\Core\Service\Interfaces\ServiceInterface;

/**
 * Interface DatabaseServiceInterface
 *
 * @package Rf\Core\Database\Interfaces
 */
interface DatabaseServiceInterface extends ServiceInterface {

    /**
     * {@inheritDoc}
     *
     * @return DatabaseConfiguration
     */
    public function getConfiguration();

    /**
     * Get the connection
     *
     * @return DatabaseConnectionInterface
     * @throws \Exception
     */
    public function getConnection();

}