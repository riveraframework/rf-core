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
 * Class I18n
 *
 * @package Rf\Core\I18n
 */
abstract class I18n {

    /** @var array $STRINGS_ */
    public static $STRINGS_;

    /** @var string Default language */
    public static $defaultLanguage;

	/** @var array Available languages */
    public static $availableLanguages = [];

    /** @var string Current Language */
    public static $currentLanguage;

    /**
     * Init the i18n module
     */
    public static function init() {

        if(!rf_empty(rf_config('i18n.languages'))) {

            self::$availableLanguages = explode(',', rf_config('i18n.languages'));
            self::$defaultLanguage = self::$availableLanguages[0];

        } else {
            self::$defaultLanguage = 'en';
        }

        // Load language files
        array_map(function ($filePath) {

            $pathParts = explode('/', $filePath);
            $fileNameParts = explode('.', $pathParts[count($pathParts) + 1]);

            self::$STRINGS_[$fileNameParts[0]] = include $filePath;

        }, glob(rf_dir('locale') . '/*.php'));

    }

    /**
     * Get the current language
     *
     * @return string
     */
    public static function getCurrentLanguage() {

        return self::$currentLanguage;

    }

    /**
     * Set the current language and locale
     *
     * @param string $language
     */
    public static function setCurrentLanguage($language) {

        if(self::isAvailableLanguage($language) === true) {
            self::$currentLanguage = $language;
        } else {
            self::$currentLanguage = self::$defaultLanguage;
        }

    }

    /**
     * Check if the language is available
     *
     * @param string $language
     *
     * @return bool
     */
    public static function isAvailableLanguage($language) {

        return in_array($language, self::$availableLanguages);

    }

    /**
     * Translate a string.
     * This function use vsprintf(), if $args is not empty the masks will be replaced by the corresponding values
     *
     * @param string $msgid
     * @param array $args
     *
     * @return string
     */
    public static function translate($msgid, $args = []) {

        if(isset(self::$STRINGS_[self::$currentLanguage][$msgid])) {
            return vsprintf(self::$STRINGS_[self::$currentLanguage][$msgid], $args);
        } else {
            return $msgid;
        }

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
    public static function translateFromDataSet($key, array $dataSet, $args = []) {

        if(isset($dataSet[$key][self::$currentLanguage])) {
            return vsprintf($dataSet[$key][self::$currentLanguage], $args);
        } else {
            return $key;
        }

    }

}
