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

use Rf\Core\Config\ConfigurationSet;

/**
 * Class I18nConfiguration
 *
 * @package Rf\Core\I18n
 */
class I18nConfiguration extends ConfigurationSet {

    /**
     * Get the available languages
     *
     * @return array
     */
    public function getAvailableLanguages() {

        return $this->get('options.languages');

    }

    /**
     * Get the default language
     *
     * @return string
     */
    public function getDefaultLanguage() {

        $availableLanguages = $this->getAvailableLanguages();

        return $availableLanguages[0];

    }

}
