<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Authentication\Exceptions;

use Rf\Core\Exception\SilentException;

/**
 * Class AuthLoginInvalidCredentialsException
 *
 * @package Rf\Core\Authentication\Exceptions
 */
class AuthLoginInvalidCredentialsException extends SilentException  {

	/** @var int Login left attempts count */
	protected $leftAttemptsCount;

	/**
	 * AuthLoginInvalidCredentialsException constructor.
	 *
	 * @param int $leftAttemptsCount
	 */
	public function __construct( $leftAttemptsCount ) {

		$this->leftAttemptsCount = $leftAttemptsCount;

		parent::__construct( self::class, 'auth-login-invalid-credentials-error' );

	}

	/**
	 * Get login left attempts count
	 *
	 * @return int
	 */
	public function getLeftAttemptsCount() {

		return $this->leftAttemptsCount;

	}

}