<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Http\Upload;

use Rf\Core\Http\Curl;

/**
 * Class UploadedFileFromUrl
 *
 * @package Rf\Core\Upload
 */
class UploadedFileFromUrl extends UploadedFile {

	/**
	 * File constructor
	 *
	 * @param string $url
	 * @param mixed $key
	 */
	public function __construct($url, $key = null) {

		$this->key = $key;

		// Get file name and tmp path from url
		$urlParts = parse_url($url);
		$pathParts = explode('/', $urlParts['path']);
		$fileName = $pathParts[count($pathParts) - 1];
		$fileTmpPath = rf_dir('IMGCENTER') . '/tmp/' . $fileName;

		// Get file content from url
		$curl = new Curl($url);
		$curl->disableSslCheck();
		$fileContent = $curl->getResults();
		file_put_contents($fileTmpPath, $fileContent);

		// Set the properties from the POST data
		parent::__construct([
			'name' => $fileName,
			'tmp_name' => $fileTmpPath,
			'type' => mime_content_type($fileTmpPath),
			'size' => filesize($fileTmpPath),
			'error' => null,
		], $key);

	}

	/**
	 * Moves the temporary file to a destination within the application
	 *
	 * @param string $destination
	 *
	 * @return bool
	 */
	public function moveTo($destination) {

		return rename($this->tmpName, $destination);

	}

}