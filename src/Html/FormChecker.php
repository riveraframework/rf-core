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
use Rf\Core\Utils\Format\Name;
use Rf\Core\Database\Tools as DatabaseTools;

/**
 * Class FormChecker
 *
 * @since 1.0
 *
 * @package Rf\Core\Html
 */
class FormChecker {

    /**
     * Get errors as HTML or JSON
     *
     * @since 1.0
     *
     * @param array $errors
     * @param bool $json
     *
     * @return string
     *
     * @TODO: Add field name to error data
     */
    public static function displayFormErrors(array $errors, $json = false) {

            $return = '<strong>Erreurs :</strong>
                        <br/>
                        <ul>';
                        foreach ($errors as $error) {
                            $return .= '<li>'.$error.'</li>';
                        }
            $return .= '</ul>';

        if(!$json) {

            return $return;

        } else {

            $response = array();
            $response['success'] = false;
            $response['errors']['reason'] = $return;

            return json_encode($response);

        }
    }

    /**
     * Check a value using a custom pattern
     *
     * @since 1.0
     *
     * @param string $value
     * @param string $pattern
     * @param string $delimiter Pattern delimiter
     * @return int
     */
    public static function checkFieldCustom($value, $pattern, $delimiter = '/') {
        return preg_match($delimiter . $pattern . $delimiter, $value);
    }

    /**
     * Check a value using a field type
     *
     * @since 1.0
     *
     * @param string $type
     * @param string $value
     * @param null|int $size
     * @return bool
     */
    private static function checkField($type, $value, $size = null) {

        if($value == 'undefined') {
            return false;
        }

        switch($type) {

            case 'captcha':
                return $value == $_SESSION['captcha'];
                break;

            case 'companyname':
                $pattern = "/^([a-zA-Zàáâãäåçèéêëìíîïðòóôõöùúûüýÿñ]+)((((\s)|(\s?(-|&amp;)\s?)|(('|\.)\s?))?([a-zA-Zàáâãäåçèéêëìíîïðòóôõöùúûüýÿñ]*)?)*)$/";
                return !preg_match($pattern, htmlspecialchars($value, ENT_COMPAT, rf_config('app.default.charset'))) ? false : true;
                break;

            case 'email':
                return !filter_var($value, FILTER_VALIDATE_EMAIL) ? false : true;
                break;

            case 'pseudo':
                $pattern = "/^([a-zA-Z0-9_.' -]{3,20})$/";
                return !preg_match($pattern, htmlspecialchars($value, ENT_COMPAT, rf_config('app.default.charset'))) ? false : true;
                break;

            case 'email-subject':
                return strlen($value) > 3 && strlen($value) < 100 ? true : false;
                break;

            case 'email-message':
                return strlen($value) > 25 ? true : false;
                break;

            case 'link':
                return !filter_var($value, FILTER_VALIDATE_URL) ? false : true;
                break;
        }
    }

    /**
     * Check a unique value using a field type
     *
     * @since 1.0
     * 
     * @param string $type
     * @param string $value
     * @param string|array $className
     * @return boolean
     */
    public static function checkUniqueField($type, $value, $className) {

        $firstCheck = self::checkField($type, $value);

        if(!$firstCheck) {
            return false;
        }

        $count = 0;
        if(is_array($className)) {

            foreach($className as $cn) {
	            $cnParts = explode('\\', $cn);
                $count += DatabaseTools::rowCount($type, $value, Name::classToTable($cnParts[count($cnParts)-1]));
            }

        } else {

	        $classNameParts = explode('\\', $className);
            $count = DatabaseTools::rowCount($type, $value, Name::classToTable($classNameParts[count($classNameParts)-1]));

        }

        return $count !== false && $count == 0 ? true : 'na';
    }

    /**
     * Check a captcha value
     *
     * @since 1.0
     *
     * @param string $value
     * @return bool
     */
    public static function checkFieldCaptcha($value) {

        return self::checkField('captcha', $value);

    }

    /**
     * Check a company name
     *
     * @since 1.0
     *
     * @param string $value
     * @return bool
     */
    public static function checkFieldCompanyName($value) {

        return self::checkField('companyname', $value);

    }

    /**
     * Check a date
     *
     * @since 1.0
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
     * @param string $time
     *
     * @return mixed
     */
    public static function checkFieldTime($time) {

        return preg_match('/^(([0-1][0-9])|(2[0-3])):([0-5][0-9]):([0-5][0-9])$/', $time);

    }

    /**
     * Check an e-mail address
     *
     * @since 1.0
     *
     * @param string $value
     * @return bool
     */
    public static function checkFieldEmail($value) {

        return self::checkField('email', $value);

    }

    /**
     * Check a unique e-mail address
     *
     * @since 1.0
     *
     * @param string $value
     * @param string|array $class
     * @return bool
     */
    public static function checkUniqueFieldEmail($value, $class) {

        return self::checkUniqueField('email', $value, $class);

    }

    /**
     * Check a username
     *
     * @since 1.0
     *
     * @param string $value
     * @return bool
     */
    public static function checkFieldUsername($value) {

	    $pattern = "/^([a-zA-Z0-9_.]{3,20})$/";
	    return !preg_match($pattern, htmlspecialchars($value, ENT_COMPAT, rf_config('app.default.charset'))) ? false : true;

    }

    /**
     * Check a slug
     *
     * @param string $value
     *
     * @return bool
     */
    public static function checkFieldSlug($value) {

	    return !preg_match('/^([a-zA-Z0-9-]{3,100})$/', htmlspecialchars($value, ENT_COMPAT, rf_config('app.default.charset'))) ? false : true;

    }

    /**
     * Check a unique username
     *
     * @since 1.0
     *
     * @param string $value
     * @param string $class
     * @return bool
     */
    public static function checkUniqueFieldUsername($value, $class) {

        return self::checkUniqueField('username', $value, $class);

    }

    /**
     * Check a password
     *
     * @param string $value
     * @param string $valueCheck
     *
     * @return bool
     */
    public static function checkFieldPassword($value, $valueCheck = null) {

        $checkLength =  strlen($value) >= 8 && strlen($value) <= 20;

        if(!$checkLength) {
            return false;
        }

        if(!isset($valueCheck)) {
        	return true;
        } else {
        	return $value == $valueCheck;
        }

    }

    /**
     * Check a name
     *
     * @param string $value
     *
     * @return bool
     */
    public static function checkFieldName($value) {

	    $rexSafety = '/[^\\\^<,"@\/\{\}\(\)\*\$%\?=>:\|;#+]+/i';

	    return preg_match($rexSafety, $value);

    }

    /**
     * Check a gender
     *
     * @param string $value
     *
     * @return bool
     */
    public static function checkFieldGender($value) {

	    return in_array(strtoupper($value), ['M', 'F', 'N'])
		        || in_array($value, [1, 2, 3])
		            ? true : false;

    }

    /**
     * Check an e-mail message
     *
     * @since 1.0
     *
     * @param string $value
     * @return bool
     */
    public static function checkFieldEmailMessage($value) {

        return self::checkField('email-message', $value);

    }

    /**
     * Check an e-mail subject
     *
     * @since 1.0
     *
     * @param string $value
     * @return bool
     */
    public static function checkFieldEmailSubject($value) {

        return self::checkField('email-subject', $value);

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
     * Check a link
     *
     * @param string $value
     *
     * @return bool
     */
    public static function checkFieldLink($value) {

        return self::checkField('link', $value);

    }

    /**
     * Check a text
     *
     * @param string $value
     * @param null|int $size
     *
     * @return bool
     */
    public static function checkFieldText($value, $size = null) {

	    if(!isset($size)) {
		    return true;
	    }

	    return strlen($value) < $size;

    }

    /**
     * Check a text
     *
     * @param string $value
     * @param null|int $min
     * @param null|int $max
     *
     * @return bool
     */
    public static function checkFieldInt($value, $min = null, $max = null) {

	    if(!preg_match('/^-?\d+$/', $value)) {
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

	    if(!preg_match('/^([1-9][0-9]*|0)(\.[0-9]+)?$/', $value)) {
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

	    if(!preg_match('/^([1-9][0-9]*|0)(\.[0-9]' . ($round ? '{2}' : '+') . ')?$/', $value)) {
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

		if(
			preg_match('/^-?([0-9]{1,2})(\.[0-9]+)?$/', $lat)
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

		if(
			preg_match('/^-?([0-9]{1,3})(\.[0-9]+)?$/', $lng)
			&& $lng >= -180
			&& $lng <= 180
		) {
			return true;
		} else {
			return false;
		}

	}

}