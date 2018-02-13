<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Upload;

/**
 * Class UploadedFile
 *
 * @TODO: Create a File class
 *
 * @package Rf\Core\Upload
 */
class UploadedFile {

	/** @var string */
	protected $name;

	/** @var string */
	protected $tmpName;

	/** @var int */
	protected $size;

	/** @var string */
	protected $extension;

	/** @var string */
	protected $type;

	/** @var string */
	protected $realType;

	/** @var string|null */
	protected $error;

	/** @var string|null */
	protected $key;

	/**
	 * File constructor
	 *
	 * @param array $file
	 * @param mixed $key
	 */
	public function __construct(array $file, $key = null) {

		$this->key = $key;

		// Set the properties from the POST data
		$this->name = $file['name'];
		$this->tmpName = $file['tmp_name'];
		$this->type = $file['type'];
		$this->size = $file['size'];
		$this->error = $file['error'];

		// Determine file extension
		$this->extension = strtolower(pathinfo($this->name, PATHINFO_EXTENSION));

		// Determine real type
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$this->realType = finfo_file($finfo, $this->tmpName);
		finfo_close($finfo);

	}

	/**
	 * Returns the real name of the uploaded file
	 *
	 * @return string
	 */
	public function getName() {

		return $this->name;

	}

	/**
	 * Returns the temporary name of the uploaded file
	 *
	 * @return string
	 */
	public function getTempName() {

		return $this->tmpName;

	}

	/**
	 * Returns the file size of the uploaded file
	 *
	 * @return int
	 */
	public function getSize() {

		return $this->size;

	}

	/**
	 * @return string
	 */
	public function getExtension() {

		return $this->extension;

	}

	/**
	 * Returns the mime type reported by the browser
	 * This mime type is not completely secure, use getRealType() instead
	 *
	 * @return string
	 */
	public function getType() {

		return $this->type;

	}

	/**
	 * Gets the real mime type of the upload file using finfo
	 *
	 * @return string
	 */
	public function getRealType() {

		return $this->realType;

	}

	/**
	 * @return string|null
	 */
	public function getError() {

		return $this->error;

	}

	/**
	 * @return string|null
	 */
	public function getKey() {

		return $this->key;

	}

	/**
	 * Moves the temporary file to a destination within the application
	 *
	 * @param string $destination
	 *
	 * @return bool
	 */
	public function moveTo($destination) {

		return move_uploaded_file($this->tmpName, $destination);

	}

}