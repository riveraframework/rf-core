<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Database\MySQL\QueryBuilder\Clauses;

/**
 * Class OrderByTrait
 *
 * @package Rf\Core\Database\MySQL\QueryBuilder\Clauses
 */
trait OrderByTrait {

    /**@var string $orderByClause*/
    public $orderByClause;

	/**
     * Set the order by clause
     *
	 * @param $fields
	 *
	 * @return $this
	 */
    public function orderBy($fields) {

        if(is_array($fields)) {
            $this->orderByClause .= implode(', ', $fields);
        } else {
            $this->orderByClause = $fields;
        }

        return $this;

    }

}