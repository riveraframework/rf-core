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
 * Class FileFactory
 *
 * @package Rf\Core\System\FileSystem
 */
class FileFactory {

    /**
     * Delete a file
     *
     * @param string $path
     *
     * @throws BaseException
     */
    public static function remove($path) {

        if(is_file($path)) {
            @unlink($path);
        } else {
            throw new BaseException(get_called_class(), 'Unable to remove the file: ' . $path);
        }

    }

}