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
 * Trait WhereTrait
 *
 * @package Rf\Core\Database\QueryEngine\Clauses
 */
trait WhereTrait {

    /** @var string $whereClause */
    public $whereClause;

    /** @var array $whereClauseValues */
    public $whereClauseValues = [];

	/**
	 * @param string $field
	 * @param string $type
	 * @param mixed $value
	 * @param null $field2
	 * @param null $operator
	 *
	 * @return $this
	 */
    protected function addWhereClauseEngine(string $field, string $type, $value = 0, $field2 = null, $operator = null) {

        // Clause type
        $validTypes = [
        	'>', '>=', '=', '<=','<', '!=',
	        'like', 'not like',
	        'in',
	        'between',
	        'is null', 'is not null',
	        'custom'
        ];
        if(!in_array($type, $validTypes)) {
        	$type = '=';
        }

        // Clause operator
        $validOperator = ['AND', 'OR'];
        if(in_array($operator, $validOperator))  {
        	$this->whereClause .= ' ' . $operator . ' ';
        }

        // Build clause
        if($type == 'custom') {

            $this->whereClause .= $field; // $field contient la requete car mandatory param

        } elseif($type == 'in') {

            $in = '';
            foreach($value as $val) {
                $in .= '?,';
                $this->whereClauseValues[] = $val;
            }
            $this->whereClause .= $field . ' IN(' . substr($in, 0, strlen($in) - 1) . ')';

        } else {

            if($field2 == null) {
                $this->whereClause .= $field . $type . '?';
                $this->whereClauseValues[] = $value;
            } else {
                $this->whereClause .= $field . $type . $field2;
            }

        }

        return $this;

    }

	/**
	 * @return $this
	 */
    public function whereBeginGroup() {

	    $this->whereClause .= ' (';

	    return $this;

    }

	/**
	 * @return $this
	 */
    public function whereEndGroup() {

	    $this->whereClause .= ') ';

	    return $this;

    }

	/**
	 * @return $this
	 */
    public function whereAnd() {

    	if(!empty($this->whereClause)) {
		    $this->whereClause .= ' AND ';
	    }

	    return $this;

    }

	/**
	 * @return $this
	 */
    public function whereOr() {

	    if(!empty($this->whereClause)) {
		    $this->whereClause .= ' OR ';
	    }

	    return $this;

    }

	/**
	 * @return $this
	 */
    public function whereCollate($encoding) {

	    $this->whereClause .= ' COLLATE ' . $encoding . ' ';

	    return $this;

    }

	/**
	 * Set the where clause
	 *
	 * @param string $whereClause
	 * @param array $values
	 *
	 * @return $this
	 */
	public function where(string $whereClause, array $values = []) {

		$this->whereClause      .= $whereClause;
		$this->whereClauseValues = array_merge($this->whereClauseValues, $values);

		return $this;

	}

	/**
	 * Add a LIKE where clause
	 * @TODO: Check for pattern check with collate
	 *
	 * @param string $field
	 * @param string $pattern
	 *
	 * @return $this
	 */
	public function whereLike(string $field, string $pattern) {

		$this->whereClause .= $field .' LIKE ? ';
		$this->whereClauseValues[] = $pattern;

		return $this;

	}

	/**
	 * Add a NOT LIKE where clause
	 *
	 * @param string $field
	 * @param string $pattern
	 *
	 * @return $this
	 */
	public function whereNotLike(string $field, string $pattern) {

		$this->whereClause .= $field .' NOT LIKE ? ';
		$this->whereClauseValues[] = $pattern;

		return $this;

	}

	/**
	 * Add a BETWEEN where clause
	 *
	 * @param string $field
	 * @param $value1
	 * @param $value2
	 *
	 * @return $this
	 */
	public function whereBetween(string $field, $value1, $value2) {

		$this->whereClause .= $field . ' BETWEEN ? AND ? ';

		$this->whereClauseValues[] = $value1;
		$this->whereClauseValues[] = $value2;

		return $this;

	}

	/**
	 * Add a IS NULL where clause
	 *
	 * @param string $field
	 *
	 * @return $this
	 */
	public function whereIsNull(string $field) {

		$this->whereClause .= $field .' IS NULL';

		return $this;

	}

	/**
	 * Add a IS NOT NULL where clause
	 *
	 * @param string $field
	 *
	 * @return $this
	 */
	public function whereIsNotNull(string $field) {

		$this->whereClause .= $field .' IS NOT NULL';

		return $this;

	}

    /* ------------------------------ CUSTOM  --------------------------------- */

    public function addWhereClauseCustom($customClause) {
	    return $this->addWhereClauseEngine($customClause, 'custom');
    }

    /* ------------------------------- IN ------------------------------------- */
    protected function addWhereClauseInEngine($field, $arrayValue, $operator = null){
	    return $this->addWhereClauseEngine($field, 'in', $arrayValue, null, $operator);
    }

    public function whereIn($field, $arrayValue) {
	    return $this->addWhereClauseInEngine($field, $arrayValue);
    }
    public function addWhereClauseIn($field, $arrayValue) {
	    return $this->whereIn($field, $arrayValue);
    }
    public function addWhereClauseAndIn($field, $arrayValue) {
	    return $this->addWhereClauseInEngine($field, $arrayValue, 'AND');
    }
    public function addWhereClauseOrIn($field, $arrayValue) {
	    return $this->addWhereClauseInEngine($field, $arrayValue, 'OR');
    }

    /* ---------------------------- COMPARISON --------------------------------- */
    protected function addWhereClauseOperatorEngine($field, $type, $value = 0, $field2 = null, $operator = null) {
        return $this->addWhereClauseEngine($field, $type, $value, $field2, $operator);
    }

    public function whereClauseSuperior($field1, $value, $field2 = null) {
	    return $this->addWhereClauseOperatorEngine($field1, '>', $value, $field2);
    }
    public function addWhereClauseSuperior($field1, $value, $field2 = null) {
	    return $this->whereClauseSuperior($field1, $value, $field2);
    }
    public function addWhereClauseAndSuperior($field1, $value, $field2 = null) {
	    return $this->addWhereClauseOperatorEngine($field1, '>', $value, $field2, 'AND');
    }
    public function addWhereClauseOrSuperior($field1, $value, $field2 = null) {
	    return $this->addWhereClauseOperatorEngine($field1, '>', $value, $field2, 'OR');
    }

	/**
	 * Add a >= where clause
	 *
	 * @param string $field1
	 * @param mixed $value
	 * @param null|string $field2
	 *
	 * @return $this
	 */
    public function whereSuperiorEqual(string $field1, $value, string $field2 = null) {

	    if($field2 == null) {
		    $this->whereClause .= $field1 . ' >= ' . '?';
		    $this->whereClauseValues[] = $value;
	    } else {
		    $this->whereClause .= $field1 . ' >= ' . $field2;
	    }

	    return $this;

    }

    public function whereEqual($field1, $value, $field2 = null) {
        return $this->addWhereClauseOperatorEngine($field1, '=', $value, $field2);
    }
    public function addWhereClauseEqual($field1, $value, $field2 = null) {
	    return $this->whereEqual($field1, $value, $field2);
    }
    public function andWhereEqual($field1, $value, $field2 = null) {
	    return $this->addWhereClauseOperatorEngine($field1, '=', $value, $field2, 'AND');
    }
    public function addWhereClauseAndEqual($field1, $value, $field2 = null) {
	    return $this->andWhereEqual($field1, $value, $field2);
    }
    public function orWhereEqual($field1, $value, $field2 = null) {
	    return $this->addWhereClauseOperatorEngine($field1, '=', $value, $field2, 'OR');
    }
    public function addWhereClauseOrEqual($field1, $value, $field2 = null) {
	    return $this->orWhereEqual($field1, $value, $field2);
    }

	/**
	 * Add a <= where clause
	 *
	 * @param string $field1
	 * @param mixed $value
	 * @param null|string $field2
	 *
	 * @return $this
	 */
	public function whereInferiorEqual(string $field1, $value, string $field2 = null) {

		if($field2 == null) {
			$this->whereClause .= $field1 . ' <= ' . '?';
			$this->whereClauseValues[] = $value;
		} else {
			$this->whereClause .= $field1 . ' <= ' . $field2;
		}

		return $this;

	}

    public function whereInferior($field1, $value, $field2 = null) {
	    return $this->addWhereClauseOperatorEngine($field1, '<', $value, $field2);
    }
    public function addWhereClauseInferior($field1, $value, $field2 = null) {
	    return $this->whereInferior($field1, $value, $field2);
    }
    public function addWhereClauseAndInferior($field1, $value, $field2 = null) {
	    return $this->addWhereClauseOperatorEngine($field1, '<', $value, $field2, 'AND');
    }

    public function whereDifferent($field1, $value, $field2 = null) {
	    return $this->addWhereClauseOperatorEngine($field1, '!=', $value, $field2);
    }
    public function addWhereClauseDifferent($field1, $value, $field2 = null) {
	    return $this->whereDifferent($field1, $value, $field2);
    }
    public function addWhereClauseAndDifferent($field1, $value, $field2 = null) {
	    return $this->addWhereClauseOperatorEngine($field1, '!=', $value, $field2, 'AND');
    }

}