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

use Rf\Core\Utils\Format\Exceptions\JsonDecodeException;

/**
 * Class Json
 *
 * @package Rf\Core\Utils\Format
 */
abstract class Json {

	/**
	 * Encode data in a json string
	 *
	 * @param mixed $value
	 * @param int $options
	 * @param int $depth
	 *
	 * @return string
	 * @throws JsonDecodeException
	 */
	public static function encode($value, $options = 0, $depth = 512) {

		$encodedJson = json_encode($value, $options, $depth);

		if(!$encodedJson) {
			throw new JsonDecodeException(json_last_error_msg(), json_last_error());
		}

		return $encodedJson;

	}

	/**
	 * Decode a json string to an array
	 *
	 * @param $jsonString
	 * @param int $options
	 * @param int $depth
	 *
	 * @return array
	 * @throws JsonDecodeException
	 */
	public static function toArray($jsonString, $depth = 512, $options = 0) {

		$decodedJson = json_decode($jsonString, true, $depth, $options);

		if(!$decodedJson) {
			throw new JsonDecodeException(json_last_error_msg(), json_last_error());
		}

		return $decodedJson;

	}

	/**
	 * Decode a json string to an object
	 *
	 * @param $jsonString
	 * @param int $options
	 * @param int $depth
	 *
	 * @return \stdClass
	 * @throws JsonDecodeException
	 */
	public static function toObject($jsonString, $depth = 512, $options = 0) {

		$decodedJson = json_decode($jsonString, false, $depth, $options);

		if(!$decodedJson) {
			throw new JsonDecodeException(json_last_error_msg(), json_last_error());
		}

		return $decodedJson;

	}

}