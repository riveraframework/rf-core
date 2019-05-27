<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Exception;

/**
 * Class ConfigurationException
 *
 * @package Rf\Core\Exception
 */
class ConfigurationException extends BaseException {

    /**
     * Main method
     */
    protected function call() {

        $this->debug();

    }

}