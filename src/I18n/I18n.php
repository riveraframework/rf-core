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

    /**
     * @var array $STRINGS_
     * @since 1.0
     */
    public static $STRINGS_;

    /**
     * @var string $stringsFile
     * @since 1.0
     */
    public static $stringsFile = 'strings.lang.php';

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

        self::$defaultLanguage = rf_config('app.default.language');
        self::$availableLanguages = explode(',', rf_config('app.available-languages'));

        // Load variables
        self::$STRINGS_ = @include rf_dir('locale') . self::$stringsFile;

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
    public static function translate($msgid, $args = array()) {

        if(isset(self::$STRINGS_[$msgid][self::$currentLanguage])) {
            return vsprintf(self::$STRINGS_[$msgid][self::$currentLanguage], $args);
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
