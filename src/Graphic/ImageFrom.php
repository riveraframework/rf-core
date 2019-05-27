<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Graphic;

use Image;

/**
 * Class ImageFrom
 *
 * @since 1.0
 *
 * @package Rf\Core\Graphic
 *
 * @TODO: Merge with image
 */
class ImageFrom extends Image {
    
    /**
     * Create a new ImageFrom from a file with a specified format
     *
     * @since 1.0
     *
     * @param string $type (jpeg|png)
     * @param string $path Path to the source image file
     *
     * @TODO: Get type from file name
     */
    public function __construct($type, $path) {

        $this->type = $type;
        $this->creatFromFile($path);

    }

    /**
     * Create a new image from a file
     *
     * @since 1.0
     *
     * @param string $path Source file path
     * @return void
     */
    protected function creatFromFile($path) {

        if(in_array($this->type, array('jpeg','png','gif'))) {
            $functionName  = 'imagecreatefrom'.$this->type;
            $this->image = $functionName($path);
        } else {
            $this->image = null;
        }

    }

    /**
     * Save image to the specified file path
     *
     * @since 1.0
     *
     * @param string $path Target file path
     * @return void
     */
    public function save($path) {

        $functionName = 'image'.  $this->type;
        $functionName($this->image, $path, 100);

    }
}