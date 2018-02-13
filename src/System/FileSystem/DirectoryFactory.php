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

/**
 * Class DirectoryFactory
 *
 * @package Rf\Core\System\FileSystem
 */
class DirectoryFactory {

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