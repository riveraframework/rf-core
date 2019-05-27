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
 * Class Captcha
 *
 * @since 1.0 Renamed from Rf_Captcha to Captcha (in \Rf\Core\Graphic namespace)
 * @since 1.0
 *
 * @package Rf\Core\Graphic
 */
abstract class Captcha {

    /**
     * Generate and display a captcha image
     *
     * @since 1.0
     *
     * @return void
     *
     * @TODO: passer les couleurs en paramètre
     * @TODO: Faire différents modèles
     * @TODO: Passer par le drawer
     * @TODO: Pb compat Firefox
     */
    public static function generate() {

        // Generate code
        $nbChar = 5;
        $captcha = '';
        $listChar = array(
            'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
            'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'
        );

        for ($i = 1; $i <= $nbChar; $i++) {
            $position = mt_rand(0,sizeof($listChar) - 1);
            $captcha .= $listChar[$position];
        }

        // Save the code in session
        $_SESSION['captcha'] = $captcha;

        // Create image
        $width = 75;
        $height = 20;
        $code_police = 5;

        $img = imageCreate($width, $height);
        $bgColor = imageColorAllocate($img, 000, 000, 000); // Noir
        $fontColor = imageColorAllocate($img, 254, 242, 000); // Jaune
        header('Content-type: image/jpeg');
        imageString(
            $img,
            $code_police,
            ($width-imageFontWidth($code_police) * strlen($_SESSION['captcha'])) / 2,
            0,
            $_SESSION['captcha'],
            $fontColor
        );
        imagejpeg($img);
        imageDestroy($img);
    }
}