<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Data\Generation;

/**
 * Class Random
 *
 * @package Rf\Core\Data\Generation
 */
class Random {

    /**
     * Generate a random uppercase letter
     *
     * @return string
     */
    public static function upperCaseLetter() {

        return chr(mt_rand(65,90));

    }

    /**
     * Generate a random lowercase letter
     *
     * @return string
     */
    public static function lowerCaseLetter() {

        return chr(mt_rand(97,122));

    }

    /**
     * Generate a random float number between a min and a max
     *
     * @param float|int $min
     * @param float|int $max
     *
     * @return float|int
     */
    public static function float($min = 0, $max = 1) {

        return $min + mt_rand() / mt_getrandmax() * ($max - $min);

    }

}