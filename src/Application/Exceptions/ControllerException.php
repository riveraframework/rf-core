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
use Rf\Core\Http\Response;

/**
 * Class ControllerException
 *
 * @package Rf\Core\Application\Exceptions
 */
class ControllerException extends DebugException {

    /**
     * Main method
     */
    protected function call() {

        parent::call();
        $httpResponse = new Response(404);
        $httpResponse->send();

    }    
}