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
 * Class HavingTrait
 *
 * @package Rf\Core\Database\QueryEngine\Clauses
 */
trait HavingTrait {

    /** @var string $havingClause */
    public $havingClause;

    /** @var array $havingClauseValues */
    public $havingClauseValues = array();
    
	/**
	 * @return $this
	 */
	public function havingBeginGroup() {

		$this->havingClause .= ' (';

		return $this;

	}

	/**
	 * @return $this
	 */
	public function havingEndGroup() {

		$this->havingClause .= ') ';

		return $this;

	}

	/**
	 * @return $this
	 */
	public function havingAnd() {

		if(!empty($this->havingClause)) {
			$this->havingClause .= ' AND ';
		}

		return $this;

	}

	/**
	 * @return $this
	 */
	public function havingOr() {

		if(!empty($this->havingClause)) {
			$this->havingClause .= ' OR ';
		}

		return $this;

	}

	/**
	 * Set the having clause
	 *
	 * @param string $havingClause
	 * @param array $values
	 *
	 * @return $this
	 */
	public function having(string $havingClause, array $values = []) {

		$this->havingClause      .= $havingClause;
		$this->havingClauseValues = array_merge($this->havingClauseValues, $values);

		return $this;

	}

	/**
	 * Add a LIKE having clause
	 * @TODO: Check for pattern check with collate
	 *
	 * @param string $field
	 * @param string $pattern
	 *
	 * @return $this
	 */
	public function havingLike(string $field, string $pattern) {

		$this->havingClause .= $field . ' LIKE ?';
		$this->havingClauseValues[] = $pattern;

		return $this;

	}

	/**
	 * Add a NOT LIKE having clause
	 *
	 * @param string $field
	 * @param string $pattern
	 *
	 * @return $this
	 */
	public function havingNotLike(string $field, string $pattern) {

		$this->havingClause .= $field . ' NOT LIKE ?';
		$this->havingClauseValues[] = $pattern;

		return $this;

	}

	/**
	 * Add a BETWEEN having clause
	 *
	 * @param string $field
	 * @param $value1
	 * @param $value2
	 *
	 * @return $this
	 */
	public function havingBetween(string $field, $value1, $value2) {

		$this->havingClause .= $field . ' BETWEEN ? AND ?';

		$this->havingClauseValues[] = $value1;
		$this->havingClauseValues[] = $value2;

		return $this;

	}

	/**
	 * Add a IS NULL having clause
	 *
	 * @param string $field
	 *
	 * @return $this
	 */
	public function havingIsNull(string $field) {

		$this->havingClause .= $field .' IS NULL';

		return $this;

	}

	/**
	 * Add a IS NOT NULL having clause
	 *
	 * @param string $field
	 *
	 * @return $this
	 */
	public function havingIsNotNull(string $field) {

		$this->havingClause .= $field .' IS NOT NULL';

		return $this;

	}

	/**
	 * Add a > having clause
	 *
	 * @param string $field1
	 * @param mixed $value
	 * @param null|string $field2
	 *
	 * @return $this
	 */
	public function havingSuperior(string $field1, $value, string $field2 = null) {

		if($field2 == null) {
			$this->havingClause .= $field1 . ' > ?';
			$this->havingClauseValues[] = $value;
		} else {
			$this->havingClause .= $field1 . ' > ' . $field2;
		}

		return $this;

	}

	/**
	 * Add a >= having clause
	 *
	 * @param string $field1
	 * @param mixed $value
	 * @param null|string $field2
	 *
	 * @return $this
	 */
	public function havingSuperiorEqual(string $field1, $value, string $field2 = null) {

		if($field2 == null) {
			$this->havingClause .= $field1 . ' >= ?';
			$this->havingClauseValues[] = $value;
		} else {
			$this->havingClause .= $field1 . ' >= ' . $field2;
		}

		return $this;

	}

	/**
	 * Add a = having clause
	 *
	 * @param string $field1
	 * @param mixed $value
	 * @param null|string $field2
	 *
	 * @return $this
	 */
	public function havingEqual(string $field1, $value, string $field2 = null) {

		if($field2 == null) {
			$this->havingClause .= $field1 . ' = ?';
			$this->havingClauseValues[] = $value;
		} else {
			$this->havingClause .= $field1 . ' = ' . $field2;
		}

		return $this;

	}

	/**
	 * Add a <= having clause
	 *
	 * @param string $field1
	 * @param mixed $value
	 * @param null|string $field2
	 *
	 * @return $this
	 */
	public function havingInferiorEqual(string $field1, $value, string $field2 = null) {

		if($field2 == null) {
			$this->havingClause .= $field1 . ' <= ?';
			$this->havingClauseValues[] = $value;
		} else {
			$this->havingClause .= $field1 . ' <= ' . $field2;
		}

		return $this;

	}

	/**
	 * Add a < having clause
	 *
	 * @param string $field1
	 * @param mixed $value
	 * @param null|string $field2
	 *
	 * @return $this
	 */
	public function havingInferior(string $field1, $value, string $field2 = null) {

		if($field2 == null) {
			$this->havingClause .= $field1 . ' < ?';
			$this->havingClauseValues[] = $value;
		} else {
			$this->havingClause .= $field1 . ' < ' . $field2;
		}

		return $this;

	}

	/**
	 * Add a != having clause
	 *
	 * @param string $field1
	 * @param mixed $value
	 * @param null|string $field2
	 *
	 * @return $this
	 */
	public function havingDifferent(string $field1, $value, string $field2 = null) {

		if($field2 == null) {
			$this->havingClause .= $field1 . ' != ?';
			$this->havingClauseValues[] = $value;
		} else {
			$this->havingClause .= $field1 . ' != ' . $field2;
		}

		return $this;

	}

}