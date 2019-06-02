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

use Rf\Core\System\FileSystem\DirectoryFactory;
use ZipArchive;

/**
 * Class Log
 *
 * @TODO: Move options in a "log" section
 * 
 * @package Rf\Core\Log
 */
class Log {

    const TYPE_DEBUG = 'debug';
    const TYPE_ERROR = 'error';
    const TYPE_INFO = 'info';
    const TYPE_WARNING = 'warning';

    /** @var string $type */
    protected $type;

    /** @var string $message */
    protected $message;

    /** @var int $logNb */
    private static $logNb;

    /** @var array $logFiles */
    private static $logFiles;

    /**
     * Create a new Log and save it in the current log file
     *
     * @param $type
     * @param $message
     *
     * @throws \Exception
     */
    public function __construct($type, $message) {

        $this->type = $type;
        $this->message = $message;
        $this->save();

    }

    /**
     * Save a new entry in the current log file
     *
     * @throws \Exception
     */
    public function save() {

        if(!is_dir(rf_dir('logs'))) {

            try {
                DirectoryFactory::create(rf_dir('logs'), 0755, true);
            } catch (\Exception $e) {
                throw new \Exception('Unable to create the logs folder');
            }

        }

        // Get the current log file content
        $fileName = self::getCurrentLog();
        $fileOldContent = file_exists($fileName) ? file_get_contents($fileName) : '';

        // Save new entry in the log file
        $fileOpen = fopen($fileName, 'c');
        $fileNewContent = '[' . date('Y/m/d H:i:s') . '] - ' . $this->type . ': ' . $this->message;
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
    public static function getFileName($current = false) {

        $fileName  = rf_dir('logs') . 'log-';
        $fileName .= str_pad(self::$logNb, 2, '0', STR_PAD_LEFT);
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
    public static function getLogNb($logName) {

        preg_match('#log-[0]*(?<logNb>\d+)_c.log#', $logName, $match);
        self::$logNb = isset($match['logNb']) ? $match['logNb'] : 0;

    }

    /**
     * Get the current log file name
     *
     * @return string
     */
    public static function getCurrentLog() {

        self::getLogNb($currentLogName = count($logList = glob(rf_dir('logs').'*_c.log')) > 0 ? $logList[0] : '');

        // If the target file exists and its size is above the limit, use the next
        if(file_exists(self::getFileName(true)) && filesize(self::getFileName(true)) > rf_config('logging.max-size')) {

            rename(self::getFileName(true), self::getFileName());
            self::$logNb++;

            // If the max number of file is reached
            if(self::$logNb == rf_config('logging.max-files') + 1) {

                if(rf_config('logging.archive') == true) {

                    // Archive existing log files if MODE_ARCHIVE_LOG is activated
                    self::archiveLogs();

                } else {

                    // Write in the first file
                    self::$logNb = 0;
                    if(file_exists(self::getFileName())) {
                        rename(self::getFileName(), self::getFileName(true));
                    }
                    self::emptyLogFile();

                }

            } else {

                if(file_exists(self::getFileName())) {
                    rename(self::getFileName(), self::getFileName(true));
                }

                if(rf_config('logging.archive') == false) {
                    self::emptyLogFile();
                }

            }

        }

        // Return the path of the current file
        return self::getFileName(true);

    }

    /**
     * Archive filled logs
     */
    public static function archiveLogs() {

        self::$logFiles = glob(rf_dir('logs').'*.log');

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
                foreach(self::$logFiles as $file) {

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
                foreach(self::$logFiles as $file) {
                    unlink($file);
                }

                // Set the counter back to 0
                self::$logNb = 0;
            }
        }
    }

    /**
     * Delete the content of a log file
     */
    public static function emptyLogFile() {

        $fileName = self::getFileName(true);
        $fileOpen = fopen($fileName, 'w');
        ftruncate($fileOpen, 0);
        fclose($fileOpen);

    }

}