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

use Rf\Core\Base\Exceptions\DebugException;
use Rf\Core\Log\LogService;

/**
 * Class FileModelGenerator
 *
 * @package Rf\Core\System\FileSystem
 */
class File {

    /** @var string */
    protected $fileContent = '';

    /** @var string */
    protected $filePath = '';

    /**
     * Set the default file path
     *
     * @param string $filePath
     */
    public function setFilePath($filePath) {

        $this->filePath = $filePath;

    }

    /**
     * Prepend content to the file
     *
     * @param $content
     */
    public function prepend($content) {

        $this->fileContent = $content . $this->fileContent;

    }

    /**
     * Append content to the file
     *
     * @param $content
     */
    public function append($content) {

        $this->fileContent .= $content;

    }

    /**
     * Write the file
     *
     * @param string $filePath
     * @param string $mode
     *
     * @return bool|int
     * @throws DebugException
     */
    public function write($mode = 'w') {

        if(empty($this->filePath)) {
            throw new DebugException(LogService::TYPE_ERROR, 'Unable to write the file, path not set');
        }

        // Write the file content
        $fileOpen = fopen($this->filePath, $mode);
        $write = fputs($fileOpen, $this->fileContent);
        fclose($fileOpen);

        return $write;

    }

}