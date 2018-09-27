<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\System\Performance;

/**
 * Class Benchmark
 *
 * @package Rf\Core\System\Performance
 */
class Benchmark {

    /** @var array $times */
	static $times = [];

    /**
     * Init the Benchmark module
     */
	public static function init() {

	    defined('APPLICATION_START') || define('APPLICATION_START', microtime(true));

	    self::$times[] = [APPLICATION_START, 'init'];

    }

    /**
     * Get logged times
     *
     * @return array
     */
    public static function getTimes() {

	    return static::$times;

    }

    /**
     * Log a time associated with a message
     *
     * @param string $message
     */
	public static function log($message) {

        if(function_exists('rf_config') && !rf_config('options.benchmark')) {
            return;
        }

	    self::$times[] = [microtime(true), $message];

    }

    /**
     * Display the list of logged times
     */
    public static function display() {

	    $lastTime = APPLICATION_START;
	    foreach (self::$times as $time) {

	        $timeSpent = $time[0] - $lastTime;

            static::displayLine($time[0], $time[1], $timeSpent);

            $lastTime = $time[0];

        }

        $endTime = microtime(true);
        static::displayLine($endTime, 'end', $endTime - APPLICATION_START);

    }

    /**
     * Display a log line
     *
     * @param float $time
     * @param string $message
     * @param float $timeSpent
     */
    protected static function displayLine($time, $message, $timeSpent) {

        echo $time . ' - ' . $message . ' (' . round($timeSpent * 1000, 2) . 'ms)' .
            (defined('APPLICATION_TYPE') && APPLICATION_TYPE === 'cron' ? PHP_EOL : '<br/>');

    }

}