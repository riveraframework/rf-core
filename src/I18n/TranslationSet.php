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

use Rf\Core\I18n\Interfaces\TranslationSetInterface;

/**
 * Class TranslationSet
 *
 * @package Rf\Core\I18n
 */
abstract class TranslationSet implements TranslationSetInterface {

    /**
     * Get the translation for a string
     *
     * @param string $string
     * @param array $params
     *
     * @return string
     */
	public function t($string, array $params = []) {

	    return _t($string, $this->getTranslations(), $params);

    }

}