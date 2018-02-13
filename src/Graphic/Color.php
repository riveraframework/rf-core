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

/**
 * Class Color
 *
 * @since 1.0
 *
 * @package Rf\Core\Graphic
 */
class Color {

    /**
     * @var string Hexadecimal red value
     * @since 1.0
     */
    public $rH;

    /**
     * @var string Hexadecimal green value
     * @since 1.0
     */
    public $gH;

    /**
     * @var string Hexadecimal blue value
     * @since 1.0
     */
    public $bH;

    /**
     * @var number Decimal red value
     * @since 1.0
     */
    public $rD;

    /**
     * @var number Decimal green value
     * @since 1.0
     */
    public $gD;

    /**
     * @var number Decimal blue value
     * @since 1.0
     */
    public $bD;

    /**
     * Create a new object color
     *
     * @since 1.0
     *
     * @param null|string $color ex: #ff002c
     */
    public function __construct($color = null) {
        if(substr($color, 0, 1) == '#') {
            $this->rH = substr($color, 1, 2);
            $this->gH = substr($color, 3, 2);
            $this->bH = substr($color, 5, 2);
            $this->rD = hexdec($this->rH);
            $this->gD = hexdec($this->gH);
            $this->bD = hexdec($this->bH);
        }
    }

    /**
     * Get the color in Hexadecimal format
     *
     * @since 1.0
     *
     * @return string
     */
    public function toHex() {
        return $this->rH . $this->gH . $this->bH;
    }

    /**
     * Get the color in Hexadecimal format with preceding #
     *
     * @since 1.0
     *
     * @return string
     */
    public function toHexSharp() {
        return '#' . $this->toHex();
    }

    /**
     * Get the color in Decimal with a leading #
     *
     * @since 1.0
     *
     * @return array
     */
    public function toRGB() {
        return array('r' => $this->rD, 'g' => $this->gD, 'b' => $this->bD);
    }
}