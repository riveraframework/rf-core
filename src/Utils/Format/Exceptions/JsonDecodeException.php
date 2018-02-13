<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Utils\Format\Exceptions;

/**
 * Class JsonDecodeException
 *
 * @package Rf\Core\Exception
 */
class JsonDecodeException extends \Exception {

    /**
     * Create a new Exception
     *
     * @param string $message
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct($message, $code = 0, \Exception $previous = null) {

        parent::__construct($message, $code, $previous);

    }

}