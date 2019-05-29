<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Utils\Format;

/**
 * Class StringTransform
 *
 * @package Rf\Core\Utils\Format
 */
abstract class StringTransform {

    // @TODO: Make exceptions customizable

    /** @var array Exceptions for the toPascalCase and toCamelCase method */
    private static $pascalCamelCaseExceptions = [
        'Oauth' => 'OAuth'
    ];

    /** @var array Exceptions for the classToTable method */
    private static $snakeKebabCaseExceptions = [
        'o_auth' => 'oauth'
    ];

    /**
     * Transform a string to camel case
     * e.g: my_awesome_string -> MyAwesomeString
     *
     * @param string $string
     *
     * @return string
     */
    public static function toPascalCase($string) {

        // Transform the string
        $newString = preg_replace('/[\s\-\._]+/', ' ', trim($string));
        $newString = str_replace(' ', '', ucwords($newString));

        // Handle exceptions
        foreach(self::$pascalCamelCaseExceptions as $string => $replacement) {
            $newString = str_replace($string, $replacement, $newString);
        }

        return $newString;

    }

    /**
     * Transform a string to camel case
     * e.g: My awesome string -> myAwesomeString
     *
     * @param string $string
     *
     * @return string
     */
    public static function toCamelCase($string) {

        // Transform the string
        $newString = self::toPascalCase($string);

        // Replace the first letter by lowercase
        $newString = lcfirst($newString);

        return $newString;

    }

    /**
     * Transform a string to snake case
     * e.g: myAwesomeString -> my_awesome_string
     * e.g: My awesome.str-ing -> my_awesome_string
     *
     * @param string $string
     *
     * @return string
     */
    public static function toSnakeCase($string) {

        // Transform the string
        $newString = lcfirst(trim($string));
        $newString = str_replace(
            ['A' ,'B' ,'C' ,'D' ,'E' ,'F' ,'G' ,'H' ,'I' ,'J' ,'K' ,'L' ,'M' ,'N' ,'O' ,'P' ,'Q' ,'R' ,'S' ,'T' ,'U' ,'V' ,'W' ,'X' ,'Y' ,'Z'],
            ['_a','_b','_c','_d','_e','_f','_g','_h','_i','_j','_k','_l','_m','_n','_o','_p','_q','_r','_s','_t','_u','_v','_w','_x','_y','_z'],
            $newString
        );
        $newString = preg_replace('/[\s\-\._]+/', '_', $newString);

        // Handle exceptions
        foreach(self::$snakeKebabCaseExceptions as $string => $replacement) {
            $newString = str_replace($string, $replacement, $newString);
        }

        return $newString;

    }

    /**
     * Transform a string to kebab case
     * e.g: myAwesomeString -> my-awesome-string
     * e.g: My awesome.str-ing -> my-awesome-string
     *
     * @param string $string
     *
     * @return string
     */
    public static function toKebabCase($string) {

        // Transform the string
        $newString = self::toSnakeCase($string);
        $newString = str_replace('_', '-', $newString);

        return $newString;

    }
    
}