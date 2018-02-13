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
 * Class DataException
 *
 * @package Rf\Core\Exception
 */
class DataException extends BaseException  {

    /** @var mixed Exception data */
    public $data;

    /**
     * Create a new Exception
     *
     * @param string $type
     * @param int $message
     * @param mixed $data
     * @param int $code
     */
    public function __construct($type, $message, $data, $code = 0) {

	    $this->data = $data;

        parent::__construct($type, $message, $code);

    }

}