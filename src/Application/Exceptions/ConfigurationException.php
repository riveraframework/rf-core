<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Application\Exceptions;

use Rf\Core\Base\Exceptions\DebugException;

/**
 * Class ConfigurationException
 *
 * @package Rf\Core\Application\Exceptions
 */
class ConfigurationException extends DebugException {

    /**
     * Main method
     */
    protected function call() {

        $this->debug();

    }

}