<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Http\Responses;

use Rf\Core\Http\Response;

/**
 * Class FileResponse
 *
 * @package Rf\Core\Http\Responses
 */
class FileResponse extends Response {

    /** @var string File path */
    protected $file;

    /**
     * Set the path of the file to serve
     *
     * @param string $file Path of the file to serve
     */
    public function setFile($file) {

        $this->file = $file;

    }

    /**
     * Send the response (serve the file)
     */
    public function send() {

        if(!empty($this->httpCode) && in_array($this->httpCode, array_keys(self::$availableHttpCodes))) {
            header('HTTP/' . self::$defaultHttpVersion . ' ' . $this->httpCode . ' ' . self::$availableHttpCodes[$this->httpCode]);
        } else {
            return;
        }

        foreach ($this->headers as $option => $value) {
            header($option . ': ' . $value);
        }

        readfile($this->file);

        exit;

    }

}