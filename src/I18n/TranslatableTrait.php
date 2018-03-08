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
 * Class TranslatableTrait
 *
 * @package Rf\Core\I18n
 */
trait TranslatableTrait {

	/**
	 * Get Default class translation
	 *
	 * @return array
	 */
	public static function getTranslations() {

		// Build the translation repository class name
		$transClassName = substr_replace(static::class, '\Translations\\', strrpos(static::class, '\\'), 1) . 'Translations';

		// Return the translations array from the associated repository if the class exists
		if(class_exists($transClassName)) {

			/** @var $transClassName TranslationRepository */
			return $transClassName::get();

		}

		return [];

	}

}