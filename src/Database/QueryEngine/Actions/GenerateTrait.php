<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Database\QueryEngine\Actions;

/**
 * Trait GenerateTrait
 *
 * @package Rf\Core\Database\QueryEngine\Actions
 */
trait GenerateTrait {

    /**
     * Generate the query string for a select query
     *
     * @return null|string
     */
    protected function generateSelect() {

        if(empty($this->fields)) {
            $this->fields = ['*'];
        }

        if(!empty($this->tables)) {

            $query  = 'SELECT ' . implode(',', $this->fields) . ' FROM ';

            foreach ($this->tables as $key => $table) {

                if($key !== 0) {
                    $query .= ',';
                }

                if(!empty($this->database)) {
                    $query .= '`' . $this->database . '`.`' . $table[0] . '`';
                } elseif(strpos($table[0], '(') === 0) {
                    $query .= $table[0];
                } else {
                    $query .= '`' . $table[0] . '`';
                }

                $query .= !empty($table[1]) ? ' AS ' . $table[1] . ' ' : ' ';

            }

            if(!empty($this->joinClause)) {
                $query .= $this->joinClause . ' ';
            }

            if(!empty($this->whereClause)) {
                $query .= 'WHERE ' . $this->whereClause . ' ';
            }

            if(!empty($this->groupByClause)) {
                $query .= 'GROUP BY ' . implode(', ', $this->groupByClause) . ' ';
            }

            if(!empty($this->havingClause)) {
                $query .= 'HAVING ' . $this->havingClause . ' ';
            }

            if(!empty($this->orderByClause)) {
                $query .= 'ORDER BY ' . $this->orderByClause . ' ';
            }

            if(!empty($this->limitClause)) {
                $query .= 'LIMIT ' . $this->limitClause . ' ';
            }

            return $query;

        } else {
            return null;
        }

    }

    /**
     * Generate the query string for an insert query
     *
     * @return null|string
     */
    protected function generateInsert() {

        if(!empty($this->fields) && isset($this->tables[0][0]) && isset($this->values)) {

            $query  = 'INSERT INTO ' . (!empty($this->database) ? '`' . $this->database . '`.' : '') . '`' . $this->tables[0][0] .'` ';
            $query .= '(' . implode(',', $this->fields) . ') ';
            $query .= 'VALUES (' . $this->values . ') ';

            return $query;

        } else {
            return null;
        }

    }

    /**
     * Generate the query string for an insert query
     *
     * @return null|string
     */
    protected function generateMultiInsert() {

        if(!empty($this->fields) && isset($this->tables[0][0]) && isset($this->values)) {

            $query  = 'INSERT INTO ' . (!empty($this->database) ? '`' . $this->database . '`.' : '') . '`' . $this->tables[0][0] .'` ';
            $query .= '(' . implode(',', $this->fields) . ') ';
            $query .= 'VALUES';

            foreach($this->values as $index => $valueSet) {
            	if($index) {
		            $query .= ',';
	            }
	            $query .= ' (' . $this->values . ') ';
            }

	        if(!empty($this->updateOnDuplicate)) {
		        $query .= 'ON DUPLICATE KEY UPDATE';
		        foreach($this->fields as $index => $field) {
		        	if(!$index) {
		        		continue;
			        }
		        	$query .= $field . ' = VALUES(' . $field . ')';
		        }
            }

            return $query;

        } else {
            return null;
        }

    }

    /**
     * Generate the query string for an update query
     *
     * @return null|string
     */
    protected function generateUpdate() {

        if(!empty($this->fields) && isset($this->tables[0][0]) && isset($this->valuesVal)) {

            $count = 0;
            $query = 'UPDATE ';

            foreach ($this->tables as $key => $table) {

                if($key !== 0) {
                    $query .= ',';
                }
                if(!empty($this->database)) {
                    $query .= '`' . $this->database . '`.`' . $table[0] . '`';
                } else {
                    $query .= '`' . $table[0] . '`';
                }
                $query .= !empty($table[1]) ? ' AS ' . $table[1] . ' ' : ' ';

            }

            if(!empty($this->joinClause)) {
                $query .= $this->joinClause . ' ';
            }

            $query .= 'SET ';

            while($count < count($this->fields)) {

            	$field = $this->fields[$count];
	            $query .= $field . '=?';

                if($count != count($this->fields) - 1) {
                	$query .= ', ';
                } else {
                	$query .= ' ';
                }
                $count++;

            }
            if(isset($this->whereClause)) {
                $query .= 'WHERE ' . $this->whereClause;
            }

            return $query;

        } else {
            return null;
        }

    }

    /**
     * Generate the query string for a delete query
     *
     * @return null|string
     */
    protected function generateDelete() {

        if(!empty($this->tables)) {

            $query  = 'DELETE ' . implode(',', $this->fields) . ' FROM ';

            foreach ($this->tables as $key => $table) {

                if($key !== 0) {
                    $query .= ',';
                }
                if(!empty($this->database)) {
                    $query .= '`' . $this->database . '`.`' . $table[0] . '`';
                } else {
                    $query .= '`' . $table[0] . '`';
                }
                $query .= !empty($table[1]) ? ' AS ' . $table[1] . ' ' : ' ';

            }

            if(!empty($this->joinClause)) {
                $query .= $this->joinClause.' ';
            }

            if(isset($this->whereClause)) {
                $query .= 'WHERE ' . $this->whereClause . ' ';
            }

            return $query;

        } else {
            return null;
        }

    }

    /**
     * Generate the query string for a describe query
     *
     * @return null|string
     */
    protected function generateDescribe() {

        if(isset($this->tables[0][0])) {

            $query  = 'DESCRIBE `' . $this->database . '`.`' . $this->tables[0][0] . '` ';

            return $query;

        } else {
            return null;
        }

    }

    /**
     * Generate the value array to pass to the prepare method of the PDO object
     *
     * @return array
     */
    public function generateValueArray() {

        return array_merge($this->valuesVal, $this->whereClauseValues, $this->havingClauseValues);

    }

    /**
     * Generate the current query string
     *
     * @return null|string
     */
    public function compile() {

        switch($this->type) {

            case 'select':
                return $this->generateSelect();

            case 'insert':
                return $this->generateInsert();

            case 'multi-insert':
                return $this->generateMultiInsert();

            case 'update':
                return $this->generateUpdate();

            case 'delete':
                return $this->generateDelete();

            case 'describe':
                return $this->generateDescribe();

            default :
                return null;

        }

    }

}