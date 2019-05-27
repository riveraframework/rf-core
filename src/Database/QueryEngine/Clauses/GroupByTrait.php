<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Database\QueryEngine\Clauses;

/**
 * Class GroupByTrait
 *
 * @package Rf\Core\Database\QueryEngine\Clauses
 */
trait GroupByTrait {

    /** @var string $groupByClause */
    public $groupByClause = [];

	/**
     * Set the group by fields
     *
	 * @param string|array $fields
	 *
	 * @return $this
	 */
    public function groupBy($fields) {

        if(!is_array($fields)) {
            $fields = explode(',', (string)$fields);
        }

        $this->groupByClause = $fields;

	    return $this;

    }

	/**
     * Add group by fields
     *
	 * @param string|array $fields
	 *
	 * @return $this
	 */
    public function addGroupBy($fields) {


        if(!is_array($fields)) {
            $this->groupByClause[] = (string)$fields;
        } else {
            $this->groupByClause = array_merge($this->groupByClause, $fields);
        }

	    return $this;

    }

}