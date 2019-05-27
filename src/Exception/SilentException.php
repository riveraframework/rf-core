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
 * Class SilentException
 *
 * @package Rf\Core\Exception
 */
class SilentException extends BaseException {

	/**
	 * Override of the debug method
	 */
	protected function debug() {}

}