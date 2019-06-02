<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Http\Upload;

/**
 * Class Uploader
 *
 * @package Rf\Core\Upload
 */
class Uploader {

    /**
     * @var array Available extensions by category
     */
    public $availableExt = [
        'img' => ['gif', 'jpg', 'jpeg', 'png']
    ];

    /**
     * @var array Available MIME types by category
     */
    public $availableMime = [
        'img' => ['image/gif', 'image/jpeg', 'image/pjpeg', 'image/png']
    ];

    /**
     * Check the format of a file
     *
     * @param string $mode Category
     * @param string $fileName File name
     * @param string $fileMime File MIME type
     *
     * @return bool
     */
    public function checkFormat($mode, $fileName, $fileMime) {

        $fileExplode = array_reverse(explode('.', $fileName));
        $ext = $fileExplode[0];

        if($mode === 'img') {
            return !in_array(strtolower($ext), $this->availableExt['img']) || !in_array(strtolower($fileMime), $this->availableMime['img']) ? false : true;
        }

        return false;

    }

    /**
     * Upload a file
     *
     * @param string $source Source path
     * @param string $dest Destination path
     *
     * @return bool
     */
    public function upload($source, $dest) {

        return move_uploaded_file($source, $dest);

    }

}
