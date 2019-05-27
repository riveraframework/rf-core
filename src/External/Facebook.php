<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\External;

/**
 * Class Facebook
 *
 * @package Rf\Core\External
 */
class Facebook extends \Facebook\Facebook {

	/**@var string $type Facebook current page type*/
	public static $type;

	/**@var string $title Facebook current page title*/
	public static $title;

	/**@var string $imageUrl Facebook current page image url*/
	public static $imageUrl;

	/**
	 * Init Facebook class with the params specified in configuration file
	 */
	public function __construct() {

		parent::__construct([
			'app_id' => rf_config( 'custom.facebook-appid' ),
			'app_secret' => rf_config( 'custom.facebook-secret' ),
			'default_graph_version' => 'v2.8',
		]);

	}

	/**
	 * Set the Facebook current page type
	 *
	 * @param string $type
	 *
	 * @return void
	 */
	public static function setType( $type ) {

		self::$type = $type;

	}

	/**
	 * Get the Facebook current page type
	 *
	 * @return string
	 */
	public static function getType() {

		return self::$type;

	}

	/**
	 * Set the current Facebook page title
	 *
	 * @param string $title
	 *
	 * @return void
	 */
	public static function setTitle( $title ) {

		self::$title = $title;

	}

	/**
	 * Get the current Facebook page title
	 *
	 * @return string
	 */
	public static function getTitle() {

		return self::$title;

	}

	/**
	 * Set the current Facebook page image url
	 *
	 * @param string $imageUrl
	 *
	 * @return void
	 */
	public static function setImageUrl( $imageUrl ) {

		self::$imageUrl = $imageUrl;

	}

	/**
	 * Get the current Facebook page image url
	 *
	 * @return string
	 */
	public static function getImageUrl() {

		return self::$imageUrl;

	}

}