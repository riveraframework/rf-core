<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Mvc\Interfaces;

use Rf\Core\I18n\TranslationSet;

/**
 * Class DictionaryInterface
 *
 * @package Rf\Core\Mvc\Interfaces
 */
interface DictionaryInterface {

    /**
     * Manually set the translation set
     *
     * @param TranslationSet $translationsSet
     */
    public function setTranslationsSet(TranslationSet $translationsSet);

    /**
     * Translate a string
     *
     * @param string $string
     * @param array $params
     */
    public function t($string, array $params = []);

}