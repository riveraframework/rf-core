<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Base;

use DateTime;
use DateTimeZone;
use DateInterval;

/**
 * Class Date
 *
 * @package Rf\Core\Base
 *
 * @TODO: check timezone
 */
class Date extends DateTime {

    /** @var string Date language (useful when formatting the date using days/months) */
    public $lang;

    /** @var DateTimeZone Date timezone */
    public $dateTimezone;

    /** @var string Date format (by default SQL DATETIME format) */
    public $format = 'Y-m-d H:i:s';

    /** @var array Available languages */
    public static $acceptedLanguages = ['fr', 'en'];

    /**
     * @var array[string][int|array]int Array of duration relative to the common date parts (see below)
     *
     * <code>
     * $duration = [
     *   'second',
     *   'minute',
     *   'hour',
     *   'day',
     *   'week',
     *   'month' => [28|29|30|31],
     *   'year'  => [365|366]
     * ];
     * </code>
     */
    public static $duration = [
        'second' => 1,
        'minute' => 60,
        'hour' => 3600,
        'day' => 86400,
        'week' => 604800,
        'month' => [28 => 2419200, 29 => 2505600, 30 => 2592000, 31 => 2678400],
        'year' => [365 => 31536000, 366 => 32424000]
    ];

    /**
     * @var array[string][string]string Array of month names (short and long) (see below)
     *
     * <code>
     * $months = [
     *   '{language}' => [short|long],
     *   ...
     * ];
     * </code>
     */
    public static $months = [
        'en' => [
            'short' => ['', 'jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'],
            'long' => ['', 'january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december']
        ],
        'fr' => [
            'short' => ['', 'jan', 'fév', 'mar', 'avr', 'mai', 'jun', 'jui', 'aou', 'sep', 'oct', 'nov', 'déc'],
            'long' => ['', 'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre']
        ],
    ];

    /**
     * @var array[string][string]string Array of day names (short and long) (see below)
     *
     * <code>
     * $days = [
     *   '{language}' => [short|long],
     *   ...
     * ];
     * </code>
     */
    public static $days = [
        'en' => [
            'short' => ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'],
            'long' => ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']
        ],
        'fr' => [
            'short' => ['dim', 'lun', 'mar', 'mer', 'jeu', 'ven', 'sam'],
            'long' => ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi']
        ],
    ];

    /**
     * @var array[string][string]string Array of comparison words  (short and long) (see below)
     *
     * <code>
     * $compare = [
     *   '{language}' => [short|long],
     *   ...
     * ];
     * </code>
     */
    public static $compare = [
        'fr' => [
            'short' => ['s', 'min', 'h', 'hier', 'aujourd\'hui', 'demain','j', 'm', 'an'],
            'long' => ['seconde', 'minute', 'heure', 'hier', 'aujourd\'hui', 'demain', 'jour', 'mois', 'an']
        ],
        'en' => [
            'short' => ['s', 'min', 'h', 'yd', 'today', 'tmr', 'd', 'm', 'y'],
            'long' => ['second', 'minute', 'hour', 'yesterday', 'today', 'tomorrow', 'day', 'month', 'year']
        ]
    ];

    /**
     * Create a new Date
     *
     * <code>
     * $params = [
     *   'language' => string,  // Date language
     *   'timezone' => string,  // Date timezone
     *   ...
     * ];
     * </code>
     *
     * @param string $date Can be a timestamp, a date string or a keyword (now|yesterday|tomorrow)
     * @param string $format Date format used to parse the date
     * @param array $params Additional parameter to build the date (see above)
     *
     * @throws \Exception
     * 
     * @TODO: Auto lang management -> DateFr, DateEn ?
     */
    public function __construct($date = 'now', $format = null, array $params = []) {

        // Set date language
        if(isset($params['language']) && in_array($params['language'], self::$acceptedLanguages)) {
            $this->lang = $params['language'];
        } else {
            $this->lang = rf_current_language();
        }

        // Set date timezone
        if(isset($params['timezone'])) {
            $this->dateTimezone = new DateTimeZone($params['timezone']);
        } else {
            $this->dateTimezone = new DateTimeZone(date_default_timezone_get());
        }

        // Set date format
        if (isset($format)) {

            if ($format === 'sql') {
                $this->format = 'Y-m-d H:i:s';
            } elseif ($format === 'c') {
                $this->format = DateTime::ISO8601;
            } else {
                $this->format = $format;
            }

        }

        // Build Date object
        if (!in_array($date, ['now', 'yesterday', 'tomorrow'])) {

            if (is_numeric($date) && !isset($format)) {

                parent::__construct('now', $this->dateTimezone);
                $this->setTimestamp($date);

            } else {

                parent::__construct('now', $this->dateTimezone);
                $date = DateTime::createFromFormat($this->format, $date, $this->dateTimezone);

                if(is_a($date, 'DateTime')) {
                    $this->setTimestamp($date->getTimestamp());
                }

            }

        } else {
            parent::__construct($date, $this->dateTimezone);
        }

    }

    /**
     * Get the date year with 2 or 4 digits
     *
     * @param int $digits Desired digit number (2|4)
     *
     * @return string 
     */
    public function getYear($digits = 4) {

        return $digits == 2 ? $this->format('y') : $this->format('Y');

    }

    /**
     * Get the date month with numbers or letters (short or long)
     *
     * @param bool $num Numeric output (true: by default) or letters (false)
     * @param bool $long Short or long representation
     *
     * @return string 
     */
    public function getMonth($num = true, $long = false) {

        if (!$num) {

            if ($long) {
                return self::$months[$this->lang]['long'][$this->format('n')];
            } else {
                return self::$months[$this->lang]['short'][$this->format('n')];
            }

        } else {

            if ($long) {
                return $this->format('m');
            } else {
                return $this->format('n');
            }

        }

    }

    /**
     * Return the numeric representation of the number of day in the month:
     * 28 through 31
     *
     * @return int
     */
    public function getMonthDayTotal() {

        return (int) $this->format('t');

    }

    /**
     * Get the date day with numbers or letters (short or long)
     *
     * @param bool $num Numeric output (true: by default) or letters (false)
     * @param bool $long Short or long representation
     *
     * @return string 
     */
    public function getDay($num = true, $long = false) {

        if (!$num) {

            if ($long) {
                return self::$days[$this->lang]['long'][$this->format('w')];
            } else {
                return self::$days[$this->lang]['short'][$this->format('w')];
            }

        } else {

            if ($long) {
                return $this->format('j');
            } else {
                return $this->format('d');
            }

        }

    }
    
    /**
     * Get the numeric representation of the day of the week:
     * 1 (for Monday) through 7 (for Sunday)
     *
     * @return int
     */
    public function getWeekDay() {

        return (int) $this->format('N');

    }

    /**
     * Get the date hour number in 12 or 24h format, with or without the leading 0 (long|short)
     *
     * @param int $format Hours format (12|24)
     * @param bool $long Short or long representation
     *
     * @return string 
     */
    public function getHour($format = 24, $long = false) {

        if ($format == 12) {
            return $long ? $this->format('h') : $this->format('g');
        } else {
            return $long ? $this->format('H') : $this->format('G');
        }

    }

    /**
     * Get the date minute number
     *
     * @return string
     */
    public function getMinutes() {

        return $this->format('i');

    }

    /**
     * Get the date second number
     *
     * @return string 
     */
    public function getSeconds() {

        return $this->format('s');

    }

    /**
     * Determine the age of the date in years
     *
     * @return string
     * @throws \Exception
     *
     * @TODO: new DateTime('now', $timezone) : timezone at a param
     */
    public function getAge() {

        return $this->diff(new DateTime('now'))->y;

    }

    /**
     * Format the date with the given mask
     *
     * @param string $format Target format
     *
     * @return string
     */
    public function format($format) {

        if ($format == 'sql') {
            $format = 'Y-m-d H:i:s';
        }

        return parent::format($format);

    }

    /**
     * Add an interval ex: P7Y5M4DT4H3M2S, P10D, PT15M
     *
     * @param DateInterval|string $interval (can be a string)
     *
     * @throws \Exception
     */
    public function addFromString($interval) {

        $interval = new DateInterval($interval);

        parent::add($interval);

    }

    /**
     * Subtract an interval ex: P7Y5M4DT4H3M2S, P10D, PT15M
     *
     * @param DateInterval|string $interval
     *
     * @throws \Exception
     */
    public function subFromString($interval) {

        $interval = new DateInterval($interval);

        parent::sub($interval);

    }

    /**
     * Compare two dates.
     * Example of outputs: 1d, 4 month, 1 minute, 25 ans, 13 Octobre
     *
     * @param Date $date
     * @param bool $short
     * @param string $afterYd (date|interval|delay)
     * @param bool $withTime
     *
     * @return string
     */
    public function compare(Date $date, $short = true, $afterYd = 'date', $withTime = false) {

	    $return = '';

    	if($this == $date) {

    		$return = 'maintenant';

	    } elseif($this < $date) {

		    $diff = $this->diff($date);

		    if ($diff->s > 0) {
			    $return = $diff->s . ' ' . self::$compare[$this->lang][($short ? 'short' : 'long')][0] . ' ago';
		    }

		    if ($diff->i > 0) {
			    $return = $diff->i . ' ' . self::$compare[$this->lang][($short ? 'short' : 'long')][1] . ' ago';
		    }

		    if ($diff->h > 0) {
			    $return = $diff->h . ' ' . self::$compare[$this->lang][($short ? 'short' : 'long')][2] . ' ago';
		    }

		    if ($diff->d == 1 && $afterYd === 'delay') {
			    $return = $diff->d . ' ' . self::$compare[$this->lang][($short ? 'short' : 'long')][6];
		    } elseif ($diff->d == 1) {
			    $return = self::$compare[$this->lang][($short ? 'short' : 'long')][3];
		    }

		    if ($afterYd == 'date' && $diff->d > 1 || $diff->m > 0 || $diff->y > 0) {

			    $return = $this->getDay() . ' ' . ucwords(self::$months[$this->lang][($short ? 'short' : 'long')][$this->getMonth()]);

			    if($withTime) {
				    $return .= ' at ' . $this->format('H') . ':' . $this->format('i');
			    }

		    } else {

			    if ($diff->d > 1) {
				    $return = $diff->d . ' ' . self::$compare[$this->lang][($short ? 'short' : 'long')][6];
			    }

			    if ($diff->m > 1) {
				    $return = $diff->m . ' ' . self::$compare[$this->lang][($short ? 'short' : 'long')][7];
			    }

			    if ($diff->y > 1) {
				    $return = $diff->y . ' ' . self::$compare[$this->lang][($short ? 'short' : 'long')][8];
			    }

		    }

	    } else {

		    $diff = $date->diff($this);

		    if ($diff->s > 0) {
			    $return = 'in ' . $diff->s . ' ' . self::$compare[$this->lang][($short ? 'short' : 'long')][0];
		    }

		    if ($diff->i > 0) {
			    $return = 'in ' . $diff->i . ' ' . self::$compare[$this->lang][($short ? 'short' : 'long')][1];
		    }

		    if ($diff->h > 0) {
			    $return = 'in ' . $diff->h . ' ' . self::$compare[$this->lang][($short ? 'short' : 'long')][2];
		    }

		    if ($diff->d == 1 && $afterYd === 'delay') {
			    $return = $diff->d . ' ' . self::$compare[$this->lang][($short ? 'short' : 'long')][6];
		    } elseif ($diff->d == 1) {
			    $return = self::$compare[$this->lang][($short ? 'short' : 'long')][5];
		    }

		    if ($afterYd == 'date' && $diff->d > 1 || $diff->m > 0 || $diff->y > 0) {

		        $return = $this->getDay() . ' ' . ucwords(self::$months[$this->lang][($short ? 'short' : 'long')][$this->getMonth()]);

		        if($withTime) {
				    $return .= ' at ' . $this->format('H') . ':' . $this->format('i');
			    }

		    } else {

			    if ($diff->d > 1) {
				    $return = $diff->d . ' ' . self::$compare[$this->lang][($short ? 'short' : 'long')][6];
			    }

			    if ($diff->m > 1) {
				    $return = $diff->m . ' ' . self::$compare[$this->lang][($short ? 'short' : 'long')][7];
			    }

			    if ($diff->y > 1) {
				    $return = $diff->y . ' ' . self::$compare[$this->lang][($short ? 'short' : 'long')][8];
			    }

		    }

	    }

        return $return;

    }

    /**
     * Check if the date format is valid.
     * This function also take a minimum age parameter.
     * The available masks are:
     * d -> 01 to 30,
     * j -> 1 to 31,
     * m -> 01 to 12,
     * n -> 1 to 12,
     * Y -> 1900 to 2099,
     * g -> 1 to 12,
     * G -> 0 to 23,
     * h -> 01 to 12,
     * H -> 00 to 23,
     * i -> 00 to 59,
     * s -> 00 to 59.
     *
     * @param string $date
     * @param string $format 
     * @param string $minAge (int | now)
     *
     * @return mixed
     * @throws \Exception
     *
     * @TODO: More masks + take language into account
     */
    public static function checkDate($date, $format, $minAge = null) {

        $regex = '/^' . $format . '$/';
        $regex = str_replace(
            [
                'd',
                'j',
                'm',
                'n',
                'Y',
                'y',
                'g',
                'G',
                'h',
                'H',
                'i',
                's'
            ],
            [
                '((0[1-9])|([1-2][0-9])|(3[0-1]))',
                '(([1-2]?[0-9])|(3[0-1]))',
                '((0[1-9])|(1[0-2]))',
                '(([1-9])|(1[0-2]))',
                '((19|20)([0-9]{2}))',
                '([0-9]{2})',
                '(([1-9])|(1[0-2]))',
                '((1?[0-9])|(2[0-3]))',
                '((0[1-9])|(1[0-2]))',
                '(([0-1][0-9])|(2[0-3]))',
                '([0-5][0-9])',
                '([0-5][0-9])'
            ],
            $regex
        );

        if (!preg_match($regex, $date)) {

            return false;

        } else {

            $compDate = new Date($date, $format);
            $dateCheck = $compDate->format($format) == $date ? true : false;

            if (!isset($minAge)) {

                return $dateCheck;

            } elseif ($minAge == 'now') {

                return $compDate < new Date('now') ? false : true;

            } else {

                return !self::checkMinAge($compDate, $minAge) ? 'ty' : true;

            }

        }

    }

    /**
     * Check if the date is superior to the min age
     *
     * @param Date $date
     * @param int $minAge
     *
     * @return bool
     * @throws \Exception
     */
    public static function checkMinAge($date, $minAge) {

        return $date->getAge() < $minAge ? false : true;

    }

    /**
     * Get the age from a timestamp.
     * Available modes (units): year
     *
     * @param int $timestamp
     * @param string $mode
     *
     * @return int
     * @throws \Exception
     */
    public static function getAgeFromTimestamp($timestamp, $mode = 'year') {

        switch ($mode) {

            default:
                $date = new Date($timestamp);
                return $date->getAge();

        }

    }

    /**
     * Get the year from a timestamp (2 or 4 digits)
     *
     * @param int $timestamp
     * @param int $digits
     *
     * @return string
     * @throws \Exception
     */
    public static function getYearFromTimestamp($timestamp, $digits = 4) {

        $date = new Date($timestamp);

        return $date->getYear($digits);

    }

    /**
     * Get the month from a timestamp in numbers or letters
     *
     * @param int $timestamp
     * @param boolean $num
     * @param boolean $long
     * @param string $lang
     *
     * @return string
     * @throws \Exception
     */
    public static function getMonthFromTimestamp($timestamp, $num = true, $long = false, $lang = null) {

        $date = new Date($timestamp, null, ['lang' => $lang]);

        return $date->getMonth($num, $long);

    }

    /**
     * Get the day from a timestamp in numbers or letters
     *
     * @param int $timestamp
     * @param boolean $num
     * @param boolean $long
     * @param string $lang
     *
     * @return string
     * @throws \Exception
     */
    public static function getDayFromTimestamp($timestamp, $num = true, $long = false, $lang = null) {

        $date = new Date($timestamp, null, ['lang' => $lang]);

        return $date->getDay($num, $long);

    }

    /**
     * Get the time (hours) from a timestamp
     *
     * @param int $timestamp
     * @param int $format 12|24
     * @param boolean $long
     *
     * @return string
     * @throws \Exception
     */
    public static function getHoursFromTimestamp($timestamp, $format = 24, $long = false) {

        $date = new Date($timestamp);

        return $date->getHour($format, $long);

    }

    /**
     * Get the time (minutes) from a timestamp
     *
     * @param int $timestamp
     *
     * @return string
     * @throws \Exception
     */
    public static function getMinutesFromTimestamp($timestamp) {

        $date = new Date($timestamp);

        return $date->getMinutes();

    }

    /**
     * Get the time (seconds) from a timestamp
     *
     * @param int $timestamp
     *
     * @return string
     * @throws \Exception
     */
    public static function getSecondsFromTimestamp($timestamp) {

        $date = new Date($timestamp);

        return $date->getSeconds();

    }

    /**
     * Get an array of the years between two dates
     *
     * @param int $from
     * @param int $to
     * @param boolean $reverse true|false
     *
     * @return array 
     */
    public static function getYears($from, $to = null, $reverse = false) {

        $years = [];

        if (!isset($to) || !is_numeric($to)) {
            $to = date('Y');
        }

        for ($year = $from; $year <= $to; $year++) {
            $years[] = $year;
        }

        if ($reverse) {
            array_reverse($years);
        }

        return $years;

    }

    /**
     * Get an array with the months of a year
     *
     * @param boolean $withLeadingZero
     *
     * @return array
     */
    public static function getMonths($withLeadingZero = false) {

        if ($withLeadingZero) {

            return ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];

        } else {

            return range(0, 12);

        }

    }

    /**
     * Get an array with the days of a month
     *
     * @TODO: Improve with the month as a param
     *
     * @param boolean $withLeadingZero true|false
     *
     * @return array
     */
    public static function getDays($withLeadingZero = false) {

        if ($withLeadingZero) {

            return ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '28', '29', '30', '31'];

        } else {

            return range(0, 31);

        }

    }

    /**
     * Get an array of the hours of a day
     *
     * @param int $format 12|24
     * @param boolean $withLeadingZero true|false
     *
     * @return array
     */
    public static function getDayHours($format = 24, $withLeadingZero = false) {

        if ($format == 12) {

            if ($withLeadingZero) {

                return ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];

            } else {

                return range(0, 12);

            }

        } else {

            if ($withLeadingZero) {

                return ['00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23'];

            } else {

                return range(0, 23);

            }

        }

    }

	/**
	 * Get available timezones
	 *
	 * @return array
	 */
    public static function getTimezones() {

	    return \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);

    }

	/**
	 * Get the date object for the monday of the current week (00:00am)
	 *
	 * @param array $params
	 *
	 * @return Date
     * @throws \Exception
	 */
    public static function getFirstDayOfWeek(array $params = []) {

    	$datetime = new static('now', null, $params);
    	$datetime->sub('P' . ($datetime->format('N') - 1) . 'D');
	    $datetime->setTime(0, 0, 0);

    	return $datetime;

    }

	/**
	 * Get the date object for the sunday of the current week (00:00am)
	 *
	 * @param array $params
	 *
	 * @return Date
     * @throws \Exception
	 */
    public static function getLastDayOfWeek(array $params = []) {

    	$datetime = new static();
    	$datetime->add('P' . (7 - $datetime->format('N')) . 'D');
	    $datetime->setTime(0, 0, 0);

    	return $datetime;

    }

}
