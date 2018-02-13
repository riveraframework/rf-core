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
 * Class JoinTrait
 *
 * @package Rf\Core\Database\QueryEngine\Clauses
 */
trait JoinTrait {

    /** @var string $joinClause */
    public $joinClause = '';

	/**
	 * @param string $type
	 * @param string $table
	 * @param null $direction
	 * @param null $fields
	 *
	 * @return $this
	 */
    protected function joinEngine(string $type, string $table, $direction = null, $fields = null) {

        if($type == 'natural') {
            $this->joinClause .= 'NATURAL JOIN '.$table;
        } elseif($type == 'using') {

            $this->joinClause .= 'JOIN '.$table;
            if(isset($fields)) {
                $this->joinClause .= ' USING ('.$fields.')';
            }

        } elseif($type == 'on') {

            $this->joinClause .= 'JOIN '.$table;
            if(isset($fields)) {
                $this->joinClause .= ' ON ('.$fields.')';
            }

        } elseif($type == 'inner') {

            $this->joinClause .= 'INNER JOIN '.$table;
            if(isset($fields)) {
                $this->joinClause .= ' ON ('.$fields.')';
            }

        } elseif($type == 'outer') {

            if(in_array(strtoupper($direction), array('RIGHT', 'LEFT'))) {
                $this->joinClause .= strtoupper($direction). ' ';
            }
            $this->joinClause .= 'OUTER JOIN '.$table;
            if(isset($fields)) {
                $this->joinClause .= ' ON ('.$fields.')';
            }

        }

        return $this;

    }

	/**
	 * @param string $table
	 * @param null $fields
	 *
	 * @return $this
	 */
    public function joinNatural(string $table, $fields = null) {

	    return $this->joinEngine('natural', $table, null, $fields);

    }

	/**
	 * @param string $table
	 * @param null $fields
	 *
	 * @return $this
	 */
    public function joinInner(string $table, $fields = null) {

	    return $this->joinEngine('inner', $table, null, $fields);

    }

	/**
	 * @param string $table
	 * @param null $fields
	 *
	 * @return $this
	 */
    public function joinOn(string $table, $fields = null) {

        return $this->joinEngine('on', $table, null, $fields);

    }

	/**
	 * @param string $table
	 * @param null $fields
	 *
	 * @return $this
	 */
    public function joinOuterLeft(string $table, $fields = null) {

	    return $this->joinEngine('outer', $table, 'LEFT', $fields);

    }

	/**
	 * @param string $table
	 * @param null $fields
	 *
	 * @return $this
	 */
    public function joinUsing(string $table, $fields = null) {

	    return $this->joinEngine('using', $table, null, $fields);

    }

}