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

/**
 * Class ErrorHandler
 *
 * @package Rf\Core\Base
 * @version 1.0
 * @since 1.0
 */
class ErrorHandler {

	/**
	 * Return a formatted version of the error
	 *
	 * @param \Error $error
	 *
	 * @return string
	 */
	public static function formatError(\Error $error) {

		$trace = debug_backtrace( false );

		if(!empty($trace[0]['args'][0]->xdebug_message)) {

			return '<table><tbody><tr><td><pre>' . $trace[0]['args'][0]->xdebug_message . '</pre></td></tr></tbody></table>';

		}

		$formattedTrace = print_r( debug_backtrace( false ), true );

		$content = '
		  <table>
			  <thead><th>Item</th><th>Description</th></thead>
			  <tbody>
				  <tr>
				    <th>Error</th>
				    <td><pre>' . $error->getMessage() . '</pre></td>
				  </tr>
				  <tr>
				    <th>Errno</th>
				    <td><pre>' . $error->getCode() . '</pre></td>
				  </tr>
				  <tr>
				    <th>File</th>
				    <td>' . $error->getFile() . '</td>
				  </tr>
				  <tr>
				    <th>Line</th>
				    <td>' . $error->getLine() . '</td>
				  </tr>
				  <tr>
				    <th>Trace</th>
				    <td><pre>' . $formattedTrace . '</pre></td>
				  </tr>
			  </tbody>
		  </table>';

		return $content;

	}

}