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

use Rf\Core\Http\Response as HttpResponse;

/**
 * Class ControllerException
 *
 * @since 1.0
 *
 * @package Rf\Core\Exception
 */
class ControllerException extends BaseException {

    /**
     * Main method
     *
     * @since 1.0
     */
    protected function call() {

        parent::call();
        $httpResponse = new HttpResponse(404);
        $httpResponse->send();

    }    
}