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

    use Rf\Core\I18n\I18nContext;

    /**
     * Get the current language
     *
     * @param string $serviceName
     *
     * @return string
     * @throws \Exception
     */
    function rf_current_language($serviceName = '') {

        return rf_sp()->getI18n($serviceName)->getCurrentLanguage();

    }

    /**
     * Get the available languages
     *
     * @param string $serviceName
     *
     * @return array
     * @throws \Exception
     */
    function rf_available_languages($serviceName = '') {

        return rf_sp()->getI18n($serviceName)->getConfiguration()->getAvailableLanguages();

    }

    /**
     * Get the translation of a string from a data set
     *
     * @param string $key
     * @param I18nContext $context
     * @param array $vars
     *
     * @return string
     * @throws \Exception
     */
    function rf_t($key, I18nContext $context, array $vars = []) {

        return rf_sp()->getI18n($context->getServiceName())->translateFromDataSet($key, $context->getDataset(), $vars);

    }

    /**
     * Get the translation of a string from a data set
     *
     * @param string $key
     * @param array $dataset
     *
     * @return string
     * @throws \Exception
     */
    function _t($key, $dataset) {

        $args = func_get_args();
        array_shift($args);
        array_shift($args);

        return rf_sp()->getI18n()->translateFromDataSet($key, $dataset, $args);

    }

}
