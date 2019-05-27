<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Core;

use Rf\Core\Exception\BaseException;

/**
 * Class Core
 * 
 * @package Rf\Core\Core
 */
class Core {
    
    /**
     * Create a directory
     * 
     * @param string $dir
     *
     * @throws BaseException
     */
    public static function createDir($dir) {
        
        if(!mkdir($dir, 0755)) {
            throw new BaseException(get_called_class(), 'mkdir() -> Impossible de créer le dossier : ' . $dir);
        }
        
    }

    /**
     * Delete a directory, a file or only the directory content
     *
     * @param string $path
     * @param boolean $content true|false
     *
     * @throws BaseException
     */
    public static function remove($path, $content = false) {

        if(!$content) {
            is_file($path)
                ? @unlink($path)
                : array_map('self::remove', glob($path . '/*')) !== @rmdir($path);
        } else {
            array_map('self::remove', glob($path . '/*'));
        }

        if(!$content && (file_exists($path) || is_dir($path))) {
            throw new BaseException(get_called_class(), 'Unable to remove the element "' . $path . '"');
        }
    }

    /**
     * Cette fonction permet de récupérer le nom de la fonction qui l'appelle.
     * Le premier niveau (1) correspond à celle dans laquelle traceFunction()
     * est appellée.
     * 
     * @param int $level (1+)
     *
     * @return string
     */
    public static function traceFunction($level) {

		$trace = debug_backtrace();

		return $trace[$level]['function'];

    }
    
}