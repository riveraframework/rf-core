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

use Color;

/**
 * Class Image
 *
 * @since 1.0
 *
 * @package Rf\Core\Graphic
 *
 * @TODO: Merge with drawer
 */
class Image {

    /**
     * @var resource $image Image to transform
     * @since 1.0
     */
    protected $image;

    /**
     * @var string $bgcolor Hexadecimal color
     * @since 1.0
     */
    protected $bgcolor;

    /**
     * @var string Image format
     * @since 1.0
     */
    protected $type;

    /**
     *
     * @since 1.0
     *
     * @param int $w Image width
     * @param int $h Image height
     * @param null $bgcolor
     */
    public function __construct($w, $h, $bgcolor = null) {

        $this->create($w, $h);

        if(isset($bgcolor)) {
            $bgcolor = new Color($bgcolor);
            $this->bgcolor = imagecolorallocate($this->image, $bgcolor->rD, $bgcolor->gD, $bgcolor->bD);
        } else {
            $bgcolor = new Color('#ffffff');
            $this->bgcolor = imagecolorallocate($this->image, $bgcolor->rD, $bgcolor->gD, $bgcolor->bD);
        }
    }

    /**
     * Create a new image with the specified dimensions
     *
     * @since 1.0
     *
     * @param int $w Image width
     * @param int $h Image height
     * @return void
     */
    protected function create($w, $h) {

        $this->image = imagecreate($w, $h);

    }

    /**
     * Add a text to the image
     *
     * @since 1.0
     *
     * @param string $text
     * @param int $x
     * @param int $y
     * @param int $angle
     * @param int $size
     * @param null $color
     * @param null $font
     * @return void
     */
    public function addText($text, $x, $y, $angle = 0, $size = 15, $color = null, $font = null) {

        if(isset($color)) {
            $color = new Color($color);
            $color = imagecolorallocate($this->image, $color->rD, $color->gD, $color->bD);
        }

        $font = '../www/fonts/arial.ttf';
        imagettftext($this->image, $size, $angle, $x, $y, $color, $font, $text); // size, angle, x, y
    }

    /**
     * Add a filled rectangle to the image
     *
     * @since 1.0
     *
     * @param int $x1
     * @param int $y1
     * @param int $x2
     * @param int $y2
     * @param $color
     * @return void
     */
    public function addFilledRectangle($x1, $y1, $x2, $y2, $color) {
        $color = new Color($color);
        $color = imagecolorallocate($this->image, $color->rD, $color->gD, $color->bD);
        imagefilledrectangle($this->image, $x1, $y1, $x2, $y2, $color);
    }

    /**
     * Add a filled polygon to the image
     *
     * @since 1.0
     *
     * @param $points
     * @param $vertices
     * @param $color
     * @return void
     */
    public function addFilledPolygon($points, $vertices, $color) {
        $color = new Color($color);
        $color = imagecolorallocate($this->image, $color->rD, $color->gD, $color->bD);
        imagefilledpolygon($this->image, $points, $vertices, $color);
    }

    /**
     * Get current image width
     *
     * @since 1.0
     *
     * @return int
     */
    public function getWidth() {
        return imagesx($this->image);
    }

    /**
     * Get current image height
     *
     * @since 1.0
     *
     * @return int
     */
    public function getHeight() {
        return imagesy($this->image);
    }

    /**
     * Resize image to a given height
     *
     * @since 1.0
     *
     * @param int $height
     * @return void
     */
    public function resizeToHeight($height) {
        $ratio = $height / $this->getHeight();
        $width = $this->getWidth() * $ratio;
        $this->resize($width,$height);
    }

    /**
     * Resized image to a given width
     *
     * @since 1.0
     *
     * @param int $width
     * @return void
     */
    public function resizeToWidth($width) {
        $ratio = $width / $this->getWidth();
        $height = $this->getHeight() * $ratio;
        $this->resize($width, $height);
    }

    /**
     * Resize image to given dimensions
     *
     * @since 1.0
     *
     * @param int $width
     * @param int $height
     * @return void
     */
    public function resize($width, $height) {
        $new_image = imagecreatetruecolor($width, $height);
        imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        $this->image = $new_image;
    }
}