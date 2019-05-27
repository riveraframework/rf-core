<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\I18n;

/**
 * Class TranslationRepository
 * @package Rf\Core\I18n
 */
abstract class TranslationRepository {

	protected static $trans = [];

	public static function get() {

		return static::$trans;

	}

}