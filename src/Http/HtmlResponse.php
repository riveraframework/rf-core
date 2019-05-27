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
 * Class HtmlResponse
 *
 * @package Rf\Core\Http
 */
class HtmlResponse extends Response {

    /**
     * Create a new HTML Response
     *
     * @param int $httpCode Response HTTP version
     * @param string $content
     */
    public function __construct($httpCode, $content) {

	    parent::__construct($httpCode);

	    $this->setBody($content);
	    $this->setContentType('text/html; charset=utf-8');

    }

}