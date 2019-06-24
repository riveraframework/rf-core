<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Database\MySQL\QueryBuilder;

use Rf\Core\Database\MySQL\QueryBuilder\Actions\ExecuteSelectTrait;

/**
 * Class Select
 *
 * @package Rf\Core\Database\MySQL\QueryBuilder
 */
class Select extends Query {

    use ExecuteSelectTrait;

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

        parent::__construct('select', $tables, $database);

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