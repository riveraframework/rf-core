<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\I18n\Interfaces;

/**
 * Class TranslationRepositoryInterface
 *
 * @package Rf\Core\I18n\Interfaces
 */
interface TranslationSetInterface {

    /**
     * Get the available translations
     *
     * @return array
     */
	public static function getTranslations();

}