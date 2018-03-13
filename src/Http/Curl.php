<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Http;

use Rf\Core\Http\Exceptions\CurlException;

/**
 * Class Curl
 *
 * @package Rf\Core\Http
 */
class Curl {

	/** @var string  */
	protected $url;

	/** @var resource  */
	protected $ch;

	/** @var string  */
	protected $error;

	/** @var int  */
	protected $errno;

	/**
	 * Curl constructor.
	 *
	 * @param string $url
	 */
	public function __construct($url) {

		$this->ch = curl_init();

		$this->url = $url;
		$this->setOption(CURLOPT_URL, $url);

	}

	/**
	 * Get the last curl error
	 *
	 * @return string
	 */
	public function getError() {

		return $this->error;

	}

	/**
	 * Get the last curl error no
	 *
	 * @return string
	 */
	public function getErrno() {

		return $this->errno;

	}

	public function setMethod($method) {

		switch (strtolower($method)) {

			case 'head':
				$this->setOption(CURLOPT_NOBODY, true);
				break;

			case 'get':
				$this->setOption(CURLOPT_HTTPGET, true);
				break;

			case 'post':
				$this->setOption(CURLOPT_POST, true);
				break;

			case 'put':
				$this->setOption(CURLOPT_CUSTOMREQUEST, 'PUT');
				break;

			case 'delete':
				$this->setOption(CURLOPT_CUSTOMREQUEST, 'DELETE');
				break;

			default:
			break;

		}


	}

	public function setOption($name, $value) {

		curl_setopt($this->ch, $name, $value);

	}

	public function setOptions(array $options) {

		foreach ($options as $name => $value) {
			$this->setOption($name, $value);
		}

	}

	public function setPostData(array $postData) {

		$this->setOption(CURLOPT_POSTFIELDS, $postData);

	}

	/**
	 * Disable SSL check
	 */
	public function disableSslCheck() {

		$this->setOption(CURLOPT_SSL_VERIFYPEER, false);
		$this->setOption(CURLOPT_SSL_VERIFYHOST, false);

	}

	/**
	 * Get curl results or curl error
	 *
	 * @return string|false
	 * @throws CurlException
	 */
	public function getResults() {

		$this->setOption(CURLOPT_RETURNTRANSFER, 1);
		$this->setOption(CURLOPT_TIMEOUT, 20);
		$result = curl_exec($this->ch);

		if($result === false) {

			$this->error = curl_error($this->ch);
			$this->errno = curl_errno($this->ch);

			curl_close($this->ch);

			throw new CurlException($this, $this->error, $this->errno);

		} else {

			curl_close($this->ch);

			return $result;

		}

	}

}