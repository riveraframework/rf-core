<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\I18n {

    /**
     * Class ApplicationHelpers
     *
     * @package Rf\Core\Application
     */
    class I18nHelpers {

        /**
         * This function goal is to use the Autoloader to load the current file containing
         * useful helpers (functions)
         */
        public static function init() { }

    }

}

namespace {

    use Rf\Core\Application\ApplicationCli;
    use Rf\Core\Application\ApplicationMvc;
    use Rf\Core\Application\Components\Route;
    use Rf\Core\Application\Components\ServiceProvider;
    use Rf\Core\Base\ParameterSet;
    use Rf\Core\Http\Request;
    use Rf\Core\I18n\I18n;
    use Rf\Core\Utils\Data\Generation\Random;
    use Rf\Core\Utils\Format\Json;

    /**
     * Get the current language
     *
     * @return string
     */
    function rf_current_language() {

        return Rf\Core\I18n\I18n::$currentLanguage;

    }

    /**
     * Get the available languages
     *
     * @return array
     */
    function rf_available_languages() {

        return Rf\Core\I18n\I18n::$availableLanguages;

    }

    /**
     * Get the translation of a string
     *
     * @param string $msgid,...
     *
     * @return string
     */
    function __($msgid) {

        $args = func_get_args();
        array_shift($args);

        return I18n::translate($msgid, $args);

    }

    /**
     * Get the translation of a string from a data set
     *
     * @param string $key
     * @param array $dataset
     *
     * @return string
     */
    function _t($key, $dataset) {

        $args = func_get_args();
        array_shift($args);
        array_shift($args);

        return I18n::translateFromDataSet($key, $dataset, $args);

    }

}
