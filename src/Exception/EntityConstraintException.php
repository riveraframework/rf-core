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
 * Class EntityConstraintException
 *
 * @package Rf\Core\Exception
 */
class EntityConstraintException extends DataValidationException {

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

		$finalMessage = $message !== '' ? $message : 'Invalid data passed to entity';

		parent::__construct($data, $finalMessage, $code, $previous);

	}

}