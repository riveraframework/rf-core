<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Http\Exceptions;

use Rf\Core\Http\Curl;

/**
 * Class CurlException
 *
 * @package Rf\Core\Exception
 */
class CurlException extends \Exception {

	/** @var Curl */
	protected $request;

    /**
     * Create a new Exception
     *
     * @param Curl $request
     * @param string $message
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct($request, $message, $code = 0, \Exception $previous = null) {

    	$this->request = $request;

        parent::__construct($message, $code, $previous);

    }

	/**
	 * Get the request
	 *
	 * @return Curl
	 */
    public function getRequest() {

    	return $this->request;

    }

}