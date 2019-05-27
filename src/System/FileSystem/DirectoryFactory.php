<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\System\FileSystem;

use Rf\Core\Exception\BaseException;

/**
 * Class DirectoryFactory
 *
 * @package Rf\Core\System\FileSystem
 */
class DirectoryFactory {

    /**
     * Create a directory
     *
     * @param string $path
     * @param int $mode
     * @param bool $recursive
     *
     * @throws BaseException
     */
    public static function create($path, $mode = 0755, $recursive = false) {

        if(!mkdir($path, $mode, $recursive)) {
            throw new BaseException(get_called_class(), 'Unable to create dir: ' . $path);
        }

    }

    /**
     * Delete a directory or only the directory content
     *
     * @param string $path
     * @param boolean $contentOnly
     *
     * @throws BaseException
     */
    public static function remove($path, $contentOnly = false) {

        if(is_dir($path)) {

            if(!$contentOnly) {
                array_map('self::remove', glob($path . '/*')) !== @rmdir($path);
            } else {
                array_map('self::remove', glob($path . '/*'));
            }

        } else {
            throw new BaseException(get_called_class(), 'Unable to remove dir: ' . $path);
        }

    }

	/**
	 * Build a relative path from a string<br/>
	 * e.g: (string) abcd => (path) a/b/c/d/
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public static function buildRelativePathFromString($string) {

		$path = '';
		$string = (string) $string;

		for ($i = 0; $i < strlen($string); $i++){
			$path .= $string[$i] . '/';
		}

		return $path;

	}

}