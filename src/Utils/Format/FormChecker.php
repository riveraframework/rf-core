<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Html;

use Rf\Core\Base\Date;

/**
 * Class FormChecker
 *
 * @package Rf\Core\Html
 */
class FormChecker {

    /**
     * Check a name
     *
     * @param string $value
     *
     * @return bool
     */
    public static function checkFieldName($value) {

        $regex = '/[^\^\\<,"@\/\{\}\(\)\[\]\*\$%\?=>:\|;#\+0123456789]+/i';

        return preg_match($regex, $value);

    }

    /**
     * Check an e-mail address
     *
     * @param string $value
     * @return bool
     */
    public static function checkFieldEmail($value) {

        return !filter_var($value, FILTER_VALIDATE_EMAIL) ? false : true;

    }

    /**
     * Check a phone number
     *
     * @param string $value
     *
     * @return bool
     */
    public static function checkFieldPhone($value) {

        $value = str_replace([' ', '.', '-', '(', ')'], '', $value);
        $pattern = '/^((\+|00)\d{1,3})?\d+$/';

        return preg_match($pattern, $value);

    }

    /**
     * Check a gender
     *
     * @param string $value
     *
     * @return bool
     */
    public static function checkFieldGender($value) {

        if(
            in_array(strtolower($value), ['m', 'f', 'n', 'o'])
            || in_array(strtolower($value), ['male', 'female', 'neutral', 'other'])
            || in_array($value, [1, 2, 3])
            || in_array($value, [0, 1, 2])
        ) {
            return true;
        }

        return false;

    }

    /**
     * Check a link
     *
     * @param string $value
     *
     * @return bool
     */
    public static function checkFieldLink($value) {

        return !filter_var($value, FILTER_VALIDATE_URL) ? false : true;

    }

    /**
     * Check a slug
     *
     * @param string $value
     * @param int $minLength
     * @param int $maxLength
     *
     * @return bool
     */
    public static function checkFieldSlug($value, $minLength = 3, $maxLength = 100) {

        $regex = '/^([a-zA-Z0-9-]{' . $minLength . ',' . $maxLength . '})$/';

        return preg_match($regex, $value) ? true : false;

    }

    /**
     * Check a date
     *
     * @param string $date
     * @param string $format
     * @param null|int $minAge
     * @return mixed
     */
    public static function checkFieldDate($date, $format, $minAge = null) {

        return Date::checkDate($date, $format, $minAge);

    }

    /**
     * Check a time (H:i:s)
     *
     * @TODO: Move logic to the Date class
     *
     * @param string $time
     *
     * @return mixed
     */
    public static function checkFieldTime($time) {

        $regex = '/^(([0-1][0-9])|(2[0-3])):([0-5][0-9]):([0-5][0-9])$/';

        return preg_match($regex, $time);

    }

    /**
     * Check an int
     *
     * @param string $value
     * @param null|int $min
     * @param null|int $max
     *
     * @return bool
     */
    public static function checkFieldInt($value, $min = null, $max = null) {

        $regex = '/^-?\d+$/';

	    if(!preg_match($regex, $value)) {
		    return false;
	    }

	    if(isset($min) && $value < $min) {
		    return false;
	    }

	    if(isset($max) && $value > $max) {
		    return false;
	    }

	    return true;

    }

    /**
     * Check a number
     *
     * @param string $value
     * @param null|int $min
     * @param null|int $max
     *
     * @return bool
     */
    public static function checkFieldNumber($value, $min = null, $max = null) {

        $regex = '/^([1-9][0-9]*|0)(\.[0-9]+)?$/';

	    if(!preg_match($regex, $value)) {
		    return false;
	    }

	    if(isset($min) && $value < $min) {
		    return false;
	    }

	    if(isset($max) && $value > $max) {
		    return false;
	    }

	    return true;

    }

    /**
     * Check a price
     *
     * @param string $value
     * @param bool $round
     * @param null|int $min
     * @param null|int $max
     *
     * @return bool
     */
    public static function checkFieldPrice($value, $round = false, $min = null, $max = null) {

        $regex = '/^([1-9][0-9]*|0)(\.[0-9]' . ($round ? '{2}' : '+') . ')?$/';

	    if(!preg_match($regex, $value)) {
		    return false;
	    }

	    if(isset($min) && $value < $min) {
		    return false;
	    }

	    if(isset($max) && $value > $max) {
		    return false;
	    }

	    return true;

    }

	/**
	 * Check a latitude
	 *
	 * @param string $lat
	 *
	 * @return mixed
	 */
	public static function checkFieldLatitude($lat) {

	    $regex = '/^-?([0-9]{1,2})(\.[0-9]+)?$/';

		if(
			preg_match($regex, $lat)
			&& $lat >= -90
			&& $lat <= 90
		) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 * Check a longitude
	 *
	 * @param string $lng
	 *
	 * @return mixed
	 */
	public static function checkFieldLongitude($lng) {

	    $regex = '/^-?([0-9]{1,3})(\.[0-9]+)?$/';

		if(
			preg_match($regex, $lng)
			&& $lng >= -180
			&& $lng <= 180
		) {
			return true;
		} else {
			return false;
		}

	}

    /**
     * Check a text length
     *
     * @param string $value
     * @param int $minLength
     * @param int $maxLength
     *
     * @return bool
     */
    public static function checkFieldTextLength($value, $minLength = null, $maxLength = null) {

        $length = strlen($value);

        if(
            (isset($minLength) && $length < $minLength)
            || (isset($maxLength) && $length > $maxLength)
        ) {
            return false;
        }

        return true;

    }

}