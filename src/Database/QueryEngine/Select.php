<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Database\QueryEngine;

use Rf\Core\Database\Query;
use Rf\Core\Database\QueryEngine\Actions\ExecuteSelectTrait;

/**
 * Class Select
 *
 * @package Rf\Core\Database\QueryEngine
 */
class Select extends Query {

    use ExecuteSelectTrait;

    /** @var string Query type */
    const QUERY_TYPE = 'select';

    /** @var bool  */
    protected $cached = false;

    /** @var int  */
    protected $cachedExpires = 0;

	/**
	 * Select constructor.
	 *
     * @param string|array|Query|null $tables
     * @param string|null $database
	 */
    public function __construct($tables = null, $database = null) {

        parent::__construct(self::QUERY_TYPE, $tables, $database);

    }

	/**
	 * Activate cache for this request
	 *
	 * @param int $expires
	 */
    public function cached($expires = 0) {

    	$this->cached = true;
    	$this->cachedExpires = $expires;

    }

}