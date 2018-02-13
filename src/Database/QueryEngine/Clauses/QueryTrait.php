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
 * Class QueryTrait
 *
 * @package Rf\Core\Database\QueryEngine\Clauses
 */
trait QueryTrait {

    /** @var string $type Query type (select|insert|update|delete|describe) */
    public $type;

    /** @var array $fields */
    protected $fields = [];

    /** @var array $values */
    public $values;

    /** @var array $valuesVal */
    public $valuesVal = [];

    /**
     * Set the query type
     *
     * @param string $type Query type (select|insert|update|delete|describe)
     */
    private function setType($type) {

        $this->type = $type;

    }

	/**
	 * Set target columns
	 *
	 * @param string|array $fields
	 *
	 * @return $this
	 */
    public function fields($fields) {

        if(!is_array($fields)) {

            $this->fields = explode(',', $fields);

        } else {

            $this->fields = $fields;

        }

        return $this;

    }

	/**
	 * Add a one or more target columns
	 *
	 * @param string|array $fields
	 *
	 * @return $this
	 */
    public function addFields($fields) {

        if(!is_array($fields)) {

            $this->fields[] = (string)$fields;

        } else {

            $this->fields = array_merge($this->fields, $fields);

        }

        return $this;

    }

    /**
     * Set values for insert and update queries
     *
     * @param array $values
     *
     * @return $this
     */
    public function values(array $values) {

        if($this->type == 'insert') {

            foreach($values as $value) {
                $this->values .= '?,';
            }

            $this->values = substr($this->values, 0, strlen($this->values) - 1);

        }

        $this->valuesVal = $values;

	    return $this;

    }

}