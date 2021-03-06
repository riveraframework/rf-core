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
 * Class JsonResponse
 *
 * @package Rf\Core\Http\Responses
 */
class JsonResponse extends Response {

	/** @var array */
	protected $data;

    /**
     * JsonResponse constructor.
     *
     * @param int $code
     * @param array $data
     */
    public function __construct($code = 200, array $data = []) {

        parent::__construct($code);

        $this->data = $data;
        $this->body = json_encode($data);
        $this->setContentType('application/json; charset=utf-8');

    }

}