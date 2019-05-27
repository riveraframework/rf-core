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
 * Class ErrorMessageException
 *
 * @package Rf\Core\Exception
 */
class ErrorMessageException extends BaseException {

	/**
	 * ErrorMessageException constructor.
	 *
	 * @param string $message
	 * @param int $code
	 * @param \Exception|null $previous
	 */
	public function __construct($message = '', $code = 0, \Exception $previous = null)
	{

		parent::__construct('ErrorMessageException', $message, $code, $previous);

	}

}