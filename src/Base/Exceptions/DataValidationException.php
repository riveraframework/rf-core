<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Base\Exceptions;

use Rf\Core\Log\LogService;

/**
 * Class DataValidationException
 *
 * @package Rf\Core\Base\Exceptions
 */
class DataValidationException extends DebugException {

	/** @var array $data */
	protected $data = [];

	/**
	 * EntityConstraintException constructor.
	 *
	 * @param array $data
	 * @param string $message
	 * @param int $code
	 * @param \Exception|null $previous
	 */
	public function __construct(array $data, $message = '', $code = 0, \Exception $previous = null)
	{

		$this->data = $data;
		$finalMessage = $message !== '' ? $message : 'Invalid data';

		parent::__construct(LogService::TYPE_ERROR, $finalMessage, $code, $previous);

	}

	/**
	 * Get exception data
	 *
	 * @return array
	 */
	public function getData() {

		return $this->data;

	}

}