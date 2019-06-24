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

use Rf\Core\Service\Service;
use Rf\Core\System\FileSystem\DirectoryFactory;
use ZipArchive;

/**
 * Class Log
 *
 * @TODO: Define the default values for the log options
 * @TODO: Make the log path customizable so we can have multiple instances of the logger with different output destinations
 *
 * @package Rf\Core\Log
 */
class LogService extends Service {

    /** @var string  */
    const TYPE = 'log';

    const TYPE_DEBUG = 'debug';
    const TYPE_ERROR = 'error';
    const TYPE_INFO = 'info';
    const TYPE_WARNING = 'warning';

    /** @var LogConfiguration */
    protected $configuration;

    /** @var int $logNb */
    protected $logNb;

    /** @var array $logFiles */
    protected $logFiles;

    /**
     * {@inheritDoc}
     *
     * @param array $configuration
     */
    public function loadConfiguration(array $configuration) {

        $this->configuration = new LogConfiguration($configuration);

    }

    /**
     * {@inheritDoc}
     *
     * @return LogConfiguration
     */
    public function getConfiguration() {

        return $this->configuration;

    }

    /**
     * Create a new Log and save it in the current log file
     *
     * @param $type
     * @param $message
     *
     * @throws \Exception
     */
    public function log($type, $message) {

        // @TODO: Handle target

        if(!is_dir(rf_dir('logs'))) {

            try {
                DirectoryFactory::create(rf_dir('logs'), 0755, true);
            } catch (\Exception $e) {
                throw new \Exception('Unable to create the logs folder');
            }

        }

        // Get the current log file content
        $fileName = $this->getCurrentLog();
        $fileOldContent = file_exists($fileName) ? file_get_contents($fileName) : '';

        // Save new entry in the log file
        $fileOpen = fopen($fileName, 'c');
        $fileNewContent = '[' . date('Y/m/d H:i:s') . '] - ' . $type . ': ' . $message;
        fputs($fileOpen, $fileNewContent . PHP_EOL . $fileOldContent);
        fclose($fileOpen);

    }

    /**
     * Get a log file name
     *
     * @param bool $current
     *
     * @return string
     */
    public function getFileName($current = false) {

        $fileName  = rf_dir('logs') . 'log-';
        $fileName .= str_pad($this->logNb, 2, '0', STR_PAD_LEFT);
        if($current) {
            $fileName .= '_c';
        }
        $fileName .= '.log';

        return $fileName;

    }

    /**
     * Set the log number from a log file name
     *
     * @param string $logName
     */
    public function getLogNb($logName) {

        preg_match('/log-[0]*(?<logNb>\d+)_c.log/', $logName, $match);
        $this->logNb = isset($match['logNb']) ? $match['logNb'] : 0;

    }

    /**
     * Get the current log file name
     *
     * @return string
     */
    public function getCurrentLog() {

        $this->getLogNb($currentLogName = count($logList = glob(rf_dir('logs').'*_c.log')) > 0 ? $logList[0] : '');

        // If the target file exists and its size is above the limit, use the next
        if(file_exists($this->getFileName(true)) && filesize($this->getFileName(true)) > $this->getConfiguration()->getMaxFileSize()) {

            rename($this->getFileName(true), $this->getFileName());
            $this->logNb++;

            // If the max number of file is reached
            if($this->logNb == $this->getConfiguration()->getMaxLogFiles() + 1) {

                if($this->getConfiguration()->getIsArchiveEnabled()) {

                    // Archive existing log files if MODE_ARCHIVE_LOG is activated
                    $this->archiveLogs();

                } else {

                    // Write in the first file
                    $this->logNb = 0;
                    if(file_exists($this->getFileName())) {
                        rename($this->getFileName(), $this->getFileName(true));
                    }
                    $this->emptyLogFile();

                }

            } else {

                if(file_exists($this->getFileName())) {
                    rename($this->getFileName(), $this->getFileName(true));
                }

                if(!$this->getConfiguration()->getIsArchiveEnabled()) {
                    $this->emptyLogFile();
                }

            }

        }

        // Return the path of the current file
        return $this->getFileName(true);

    }

    /**
     * Archive filled logs
     *
     * @throws \Exception
     */
    public function archiveLogs() {

        if(class_exists('\ZipArchive')) {
            throw new \Exception('You need to install the extension php-zip to be able to archive log files');
        }

        $this->logFiles = glob(rf_dir('logs').'*.log');

        // Instantiate the ZipArchive class
        $zip = new ZipArchive();

        if(!file_exists($zipPath = rf_dir('logs').'archive-log.zip')) {

            // Create the file if it doesn't exist
            $res = $zip->open($zipPath, ZipArchive::CREATE);
        } else {

            // Open the file
            $res = $zip->open($zipPath);
        }

        $error = false;

        // If the file was created/opened
        if($res === true) {

            // Define the folder path the is going to contain the log files
            $path = date('Y').'-'.
                    date('m').'-'.
                    date('d').' '.
                    date('H').':'.
                    date('i').':'.
                    date('s');

            // Add the folder to the archive
            if($zip->addEmptyDir($path) !== false) {

                // Then for each file...
                foreach($this->logFiles as $file) {

                    // Get the file name
                    $name = array_pop(explode('/', $file));

                    // Add the file to the archive
                    if(
                        $zip->locateName($path . '/' . $name) === true
                        || $zip->addFile($file, $path.'/'.$name) === false
                    ) {
                        $error = true;
                    }

                }

            } else {
                $error = true;
            }

            $zip->close();

            // If there is no error
            if($error === false) {

                // Delete the files
                foreach($this->logFiles as $file) {
                    unlink($file);
                }

                // Set the counter back to 0
                $this->logNb = 0;
            }
        }
    }

    /**
     * Delete the content of a log file
     */
    public function emptyLogFile() {

        $fileName = $this->getFileName(true);
        $fileOpen = fopen($fileName, 'w');
        ftruncate($fileOpen, 0);
        fclose($fileOpen);

    }

}