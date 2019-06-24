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

use Rf\Core\Service\Service;

/**
 * Class I18nService
 *
 * @package Rf\Core\I18n
 */
class I18nService extends Service {

    /** @var string  */
    const TYPE = 'i18n';

    /** @var I18nConfiguration */
    protected $configuration;

    /** @var string Current Language */
    public $currentLanguage;

    /** @var array $STRINGS_ */
    public $strings;

    /**
     * 
     */
    public function load() {

        // @TODO: Improve this
        // Load language files
        array_map(function ($filePath) {

            $pathParts = explode('/', $filePath);
            $fileNameParts = explode('.', $pathParts[count($pathParts) + 1]);

            $this->strings[$fileNameParts[0]] = include $filePath;

        }, glob(rf_dir('locale') . '/*.php'));

    }

    /**
     * {@inheritDoc}
     *
     * @param array $configuration
     */
    public function loadConfiguration(array $configuration) {

        $this->configuration = new I18nConfiguration($configuration);

    }

    /**
     * {@inheritDoc}
     *
     * @return I18nConfiguration
     */
    public function getConfiguration() {

        return $this->configuration;

    }

    /**
     * Get the current language
     *
     * @return string
     */
    public function getCurrentLanguage() {

        return $this->currentLanguage;

    }

    /**
     * Set the current language and locale
     *
     * @param string $language
     */
    public function setCurrentLanguage($language) {

        if($this->isAvailableLanguage($language) === true) {
            $this->currentLanguage = $language;
        } else {
            $this->currentLanguage = $this->configuration->getDefaultLanguage();
        }

    }

    /**
     * Check if the language is available
     *
     * @param string $language
     *
     * @return bool
     */
    public function isAvailableLanguage($language) {

        return in_array($language, $this->configuration->getAvailableLanguages());

    }

    /**
     * Translate a string.
     * This function use vsprintf(), if $args is not empty the masks will be replaced by the corresponding values
     *
     * @param string $key
     * @param array $dataSet
     * @param array $args
     *
     * @return string
     */
    public function translateFromDataSet($key, array $dataSet, $args = []) {

        if(isset($dataSet[$key][$this->currentLanguage])) {
            return vsprintf($dataSet[$key][$this->currentLanguage], $args);
        } else {
            return $key;
        }

    }

}
