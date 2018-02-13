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
use Color;

/**
 * Class ImagePng
 *
 * @since 1.0 Renamed from Png to
 * @since 1.0
 *
 * @package Rf\Core\Graphic
 */
class ImagePng extends Image {

    /**
     * Create a new png image
     *
     * @since 1.0
     *
     * @param int $w Image width
     * @param int $h Image height
     */
    public function __construct($w, $h) {

        $this->create($w, $h);

        // Set a transparent background
        $color = new Color('#ffffff');
        $this->bgcolor = imagecolorallocate($this->image, $color->rD, $color->gD, $color->bD);
        imagecolortransparent($this->image, $this->bgcolor);

    }

    /**
     * Save the image to the specified file path
     *
     * @since 1.0
     *
     * @param string $path Target file path
     * @return void
     */
    public function save($path) {

        imagepng($this->image, $path);

    }
}