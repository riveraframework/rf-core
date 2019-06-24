<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Base {

    /**
     * Class BaseHelpers
     *
     * @package Rf\Core\Base
     */
    class BaseHelpers {

        /**
         * This function goal is to use the Autoloader to load the current file containing
         * useful helpers (functions)
         */
        public static function init() { }

    }

}

namespace {

    /**
     * PHP empty function wrapper
     *
     * @param mixed $var
     *
     * @return bool
     */
    function rf_empty($var) {

        return empty($var);

    }

    /**
     * Get and format the current date
     *
     * @param string $format Output date format
     *
     * @return string
     * @throws \Exception
     */
    function rf_date($format) {

        $date = new Rf\Core\Base\Date();

        return $date->format($format);

    }

    /**
     * This function convert a date string from a given format to another
     *
     * @param string $formatFrom
     * @param string $formatTo
     * @param string $date
     *
     * @return string
     * @throws \Exception
     */
    function rf_date_fromto($formatFrom, $formatTo, $date) {

        $date = new Rf\Core\Base\Date($date, $formatFrom);

        return $date->format($formatTo);

    }

    /////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////

    /**
     * Sort an array of arrays using a common key present in the child arrays. The {$type}
     * argument allows to define the check type for the values.
     *
     * @param array $arrayOfArrays Array of array to sort
     * @param string $key Key name to use to sort elements
     * @param string $type date|number|other
     * @param string $order asc|desc
     *
     * @return array
     */
    function rf_aasort(array $arrayOfArrays, $key, $type, $order = 'asc') {

        if ($type == 'date') {

            $comp = function ($a, $b) use ($key) {
                $date1 = new Rf\Core\Base\Date($a[$key]);
                $date2 = new Rf\Core\Base\Date($b[$key]);
                if ($date1 == $date2) {
                    return 0;
                }
                return ($date1 < $date2) ? -1 : 1;
            };

        } elseif ($type == 'number') {

            $comp = function ($a, $b) use ($key) {
                if ($a[$key] == $b[$key]) {
                    return 0;
                }
                return ($a[$key] < $b[$key]) ? -1 : 1;
            };

        } else {

            $comp = function ($a, $b) use ($key) {
                return strnatcmp($a[$key], $b[$key]);
            };

        }

        usort($arrayOfArrays, $comp);
        if ($order == 'desc') {
            $arrayOfArrays = array_reverse($arrayOfArrays);
        }

        return $arrayOfArrays;

    }

    /**
     * This function return the value referenced in array with given key then
     * unset this line.
     *
     * @param array $array Target array
     * @param mixed $key Key to extract
     *
     * @return mixed|false return the value or false on
     */
    function rf_array_extract(&$array, $key) {

        if(!isset($array[$key])) {
            return false;
        }

        $extractVal = $array[$key];
        unset($array[$key]);

        return $extractVal;

    }

    /**
     * Get a value in an array recursively
     *
     * @param array $array
     * @param string $key E.g: arrayKey.subArrayKey[...]
     *
     * @return mixed
     */
    function rf_array_get(array $array, $key) {

        // Split the key parts
        $keyParts = explode('.', $key);

        foreach($keyParts as $keyIndex => $keyName) {

            // Break loop if one of the key does not exist
            if(!isset($array[$keyName])) {
                break;
            }

            // Update current array with sub-array
            $array = $array[$keyName];

            if($keyIndex + 1 < count($keyParts)) {

                // Continue iteration while key is not the last
                continue;

            } else {

                // Return value
                return $array;

            }

        }

        return null;

    }

}
