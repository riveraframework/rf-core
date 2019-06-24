<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Log;

use Rf\Core\Config\ConfigurationSet;

/**
 * Class LogConfiguration
 *
 * @package Rf\Core\Log
 */
class LogConfiguration extends ConfigurationSet {

    /**
     * Get the database type
     *
     * @return array
     */
    public function getTargets() {

        return $this->get('targets');

    }

    /**
     * Returns whether the log files are archived or not
     *
     * @return bool
     */
    public function getIsArchiveEnabled() {

        return $this->get('options.archive');

    }

    /**
     * Get the maximum size of a log file
     *
     * @return int
     */
    public function getMaxFileSize() {

        return $this->get('options.max_file_size');

    }

    /**
     * Get the maximum number of log files
     *
     * @return int
     */
    public function getMaxLogFiles() {

        return $this->get('options.max_log_files');

    }

    /**
     * Get the default configuration value
     *
     * @return array
     */
    protected function getDefaultValues() {

        return [
            'targets' => [
                [
                    'type' => 'disk',
                    'path' => rf_dir('logs')
                ]
            ],
            'options' => [
                'archive' => false,
                'max_file_size' => 5120000,
                'max_log_files' => 4,
            ]
        ];

    }

}
