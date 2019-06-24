<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\System {

    /**
     * Class SystemHelpers
     *
     * @package Rf\Core\Application
     */
    class SystemHelpers {

        /**
         * This function goal is to use the Autoloader to load the current file containing
         * useful helpers (functions)
         */
        public static function init() { }

    }

}

namespace {

    use Rf\Core\Base\Exceptions\DebugException;
    use Rf\Core\Log\LogService;
    use Rf\Core\System\FileSystem\DirectoryFactory;
    use Rf\Core\System\FileSystem\FileFactory;

    /**
     * Delete a directory, a file or only the directory content
     *
     * @param string $path <p>
     * Path to delete
     * </p>
     * @param boolean $content true|false
     *
     * @throws DebugException
     */
    function rf_unlink($path, $content = false) {

        is_file($path)
            ? FileFactory::remove($path)
            : DirectoryFactory::remove($path, $content);

        // @TODO: Improve check
        if(!$content && (file_exists($path) || is_dir($path))) {
            throw new DebugException(LogService::TYPE_ERROR, 'Unable to remove the element "' . $path . '"');
        }

    }

}

