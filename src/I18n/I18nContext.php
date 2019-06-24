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
 * Class I18nContext
 *
 * @package Rf\Core\I18n
 */
class I18nContext {

    /** @var TranslationSetInterface */
    protected $translationSet;

    /** @var string */
    protected $serviceName;

    /**
     * I18nContext constructor.
     *
     * @param TranslationSetInterface $translationSet
     * @param string $serviceName
     */
    public function __construct(TranslationSetInterface $translationSet, $serviceName = '') {

        $this->translationSet = $translationSet;
        $this->serviceName = $serviceName;

    }

    /**
     * Get dataset
     *
     * @return TranslationSetInterface
     */
    public function getTranslationSet() {

        return $this->translationSet;

    }

    /**
     * Get service name
     *
     * @return string
     */
    public function getServiceName() {

        return $this->serviceName;

    }

}
