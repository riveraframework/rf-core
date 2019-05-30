<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Utils\Format;

/**
 * Class Number
 *
 * @package Rf\Core\Utils\Format
 */
class Number {

	public static function toPrice($value) {

		// @TODO: later
		return;

	}

	/**
	 * Get price value from prince string
	 *
	 * @param mixed $price
	 *
	 * @return float
	 */
	public static function getValueFromPrice($price) {

		$value = str_replace(
			[','],
			['.'],
			preg_replace(
				'/[^0-9,\.]+/',
				'',
				$price
			)
		);

		return $value;

	}

}