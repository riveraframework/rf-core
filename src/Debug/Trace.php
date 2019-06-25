<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Utils\Debug;

/**
 * Class Trace
 * 
 * @package Rf\Core\Utils\Debug
 */
class Trace {

    /**
     * Return the full debug backtrace
     *
     * @return array
     */
    public static function getFull() {

        return debug_backtrace();

    }

    /**
     * Get the name of a function from the trace
     *
     * The first level (1) is the function in which this one is called.
     * The 1 + n level will be the name of the parent function calling the current one (n).
     * 
     * @param int $level (1 (min) -> 1 + n)
     *
     * @return string
     */
    public static function traceFunction($level) {

		$trace = debug_backtrace();

		return $trace[$level]['function'];

    }
    
}