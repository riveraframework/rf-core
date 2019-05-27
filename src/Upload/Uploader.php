<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Upload;

/**
 * Class Uploader
 *
 * @since 1.0
 *
 * @package Rf\Core\Upload
 */
class Uploader {

    /**
     * @var array Available extensions by category
     * @since 1.0
     */
    public $availableExt = array(
        'img' => array('gif', 'jpg', 'jpeg', 'png')
    );

    /**
     * @var array Available MIME types by category
     * @since 1.0
     */
    public $availableMime = array(
        'img' => array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/png')
    );

    /**
     * Check the format of a file
     *
     * @since 1.0
     *
     * @param string $mode Category
     * @param string $fileName File name
     * @param string $fileMime File MIME type
     * @return bool
     */
    public function checkFormat($mode, $fileName, $fileMime) {

        $fileExplode = array_reverse(explode('.', $fileName));
        $ext = $fileExplode[0];

        if($mode === 'img') {
            return !in_array(strtolower($ext), $this->availableExt['img']) || !in_array(strtolower($fileMime), $this->availableMime['img']) ? false : true;
        }

    }

    /**
     * Upload a file
     *
     * @since 1.0
     *
     * @param string $source Source path
     * @param string $dest Destination path
     * @return bool
     */
    public function upload($source, $dest) {

        return move_uploaded_file($source, $dest);

    }
}
