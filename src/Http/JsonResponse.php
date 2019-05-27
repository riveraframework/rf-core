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

/**
 * Class JsonResponse
 *
 * @package Rf\Core\Http
 */
class JsonResponse extends Response {

	/** @var array */
	protected $data;

    /**
     * Create a new JSON Response
     *
     * @param int $httpCode Response HTTP version
     * @param array $data
     */
    public function __construct($httpCode, array $data = []) {

	    parent::__construct($httpCode);

	    $this->data = $data;
	    $this->body = json_encode($data);
	    $this->setContentType('application/json; charset=utf-8');

    }

}