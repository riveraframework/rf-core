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

use ZipArchive;

/**
 * Class Log
 * 
 * @package Rf\Core\Log
 */
class Log {

    const TYPE_DEBUG = 'debug';
    const TYPE_ERROR = 'error';
    const TYPE_INFO = 'info';
    const TYPE_WARNING = 'warning';

    /**
     * @var string $type
     */
    protected $type;

    /**
     * @var string $message
     */
    protected $message;

    /**
     * @var int $logNb
     */
    private static $logNb;

    /**
     * @var array $logFiles
     */
    private static $logFiles;

    /**
     * Create a new Log and save it in the current log file
     *
     * @param $type
     * @param $message
     */
    public function __construct($type, $message) {

        $this->type = $type;
        $this->message = $message;
        $this->save();

    }

    /**
     * Save a new entry in the current log file
     */
    public function save() {

        if(!is_dir(rf_dir('logs'))) {
            if(!mkdir(rf_dir('logs'), 0755) && rf_config('options.debug-mode'))  {
                echo 'Error: "logs" folder doesn\'t exist.';
            }
        }

        if(is_dir(rf_dir('logs'))) {

            // Get the current log file content
            $fileName = self::getCurrentLog();
            $fileOldContent = file_exists($fileName) ? file_get_contents($fileName) : '';

            // Save new entry in the log file
            $fileOpen = fopen($fileName, 'c');
            $fileNewContent = '[' . date('Y/m/d H:i:s') . '] - ' . $this->type . ': ' . $this->message;
            fputs($fileOpen, $fileNewContent . PHP_EOL . $fileOldContent);
            fclose($fileOpen);
        }
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

        // Si le fichier cible existe et que sa taille est supérieure à la limite on passe au suivant
        if(file_exists(self::getFileName(true)) && filesize(self::getFileName(true)) > rf_config('options.max-log-size')) {

            rename(self::getFileName(true), self::getFileName());
            self::$logNb++;

            // Mais si l'on a atteinte le nombre max de logs, 2 possibilités
            if(self::$logNb == rf_config('options.max-log-files')+1) {

                // Si le MODE_ARCHIVE_LOG est activé
                if(rf_config('options.archive-log-mode') == true) {

                    // On archive les logs existants
                    self::archiveLogs();

                } else {

                    // Sinon on ré-écrit simplement dans le premier
                    self::$logNb = 0;
                    if(file_exists(self::getFileName())) {
                        rename(self::getFileName(), self::getFileName(true));
                    }
                    self::emptyLogFile(true);
                }

            } else {

                if(file_exists(self::getFileName())) {
                    rename(self::getFileName(), self::getFileName(true));
                }

                if(rf_config('options.archive-log-mode') == false) {
                    self::emptyLogFile(true);
                }

            }
        }

        // Enfin on retourne le chemin du fichier courrant
        return self::getFileName(true);
    }

    /**
     * Archive filled logs
     */
    public static function archiveLogs() {

        self::$logFiles = glob(rf_dir('logs').'*.log');

        // On instancie la classe ZipArchive
        $zip = new ZipArchive();

        if(!file_exists($zipPath = rf_dir('logs').'archive-log.zip')) {

            // Si le fichier cible n'existe pas on le crée
            $res = $zip->open($zipPath, ZipArchive::CREATE);
        } else {

            // Sinon on l'ouvre
            $res = $zip->open($zipPath);
        }

        $error = false;

        // Si l'ouverture/la création a fonctionné on rentre dans la boucle 
        if($res === true) {

            // On défini le chemin du dossier qui va contenir le groupe de logs
            $path = date('Y').'-'.
                    date('m').'-'.
                    date('d').' '.
                    date('H').':'.
                    date('i').':'.
                    date('s');

            // On ajoute le dossier à l'archive
            if($zip->addEmptyDir($path) !== false) {

                // Pour chaque fichier présent...
                foreach(self::$logFiles as $file) {

                    // On récupère le nom
                    $name = array_pop(explode('/', $file));

                    // Puis on l'ajoute à l'archive
                    if($zip->locateName($path.'/'.$name) === true || $zip->addFile($file, $path.'/'.$name) === false) {
                        $error = true;
                    }

                }

            } else {
                $error = true;
            }

            $zip->close();

            // Si il n'y a pas eu d'erreur
            if($error === false) {

                // On supprime les fichiers
                foreach(self::$logFiles as $file) {
                    unlink($file);
                }

                // Et on remet le compteur à 0
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