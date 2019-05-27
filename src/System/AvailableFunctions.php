<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\System;

/**
 * Class AvailableFunctions
 *
 * @package Rf\Core\System
 */
class AvailableFunctions {

	/**
	 * Check if the shell_exec function is enabled
	 *
	 * @return bool
	 */
	public static function isShellExecEnabled() {

		return self::isEnabled('shell_exec');

	}

	/**
	 * Check if a PHP function is enabled
	 *
	 * @param string $func
	 *
	 * @return bool
	 */
	protected static function isEnabled($func) {

		return is_callable($func) && false === stripos(ini_get('disable_functions'), $func);

	}


}