<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Mvc;

use Rf\Core\Base\ParameterSet;
use Rf\Core\I18n\TranslationSet;
use Rf\Core\Mvc\Interfaces\DictionaryInterface;

/**
 * Class Dictionary
 *
 * @package Rf\Core\Mvc
 */
class Dictionary extends ParameterSet implements DictionaryInterface {

    /** @var TranslationSet */
    protected $trans;

    /**
     * Dictionary constructor.
     *
     * @param array|object $params
     * @param bool $skipObjects
     */
    public function __construct($params = [], $skipObjects = true) {

        parent::__construct($params, $skipObjects);

        // Automatically load the translation set
        $classNameWithNs = static::class;
        $classNameWithNsParts = explode('\\', $classNameWithNs);
        $className = array_pop($classNameWithNsParts);
        $transClassName = implode('\\', $classNameWithNsParts) . '\\Translations\\' . $className . 'Translations';

        if(class_exists($transClassName) && is_subclass_of($transClassName, TranslationSet::class)) {

            $this->trans = new $transClassName();

        }

    }

    /**
     * Manually set the translation set
     *
     * @param TranslationSet $translationsSet
     */
    public function setTranslationsSet(TranslationSet $translationsSet) {

        $this->trans = $translationsSet;

    }

    /**
     * Translate a string
     *
     * @param string $string
     * @param array $params
     */
    public function t($string, array $params = []) {

        $this->trans->t($string, $params);

    }

}