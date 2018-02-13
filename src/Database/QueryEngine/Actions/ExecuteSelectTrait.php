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

use Rf\Core\Database\PDO;
use Rf\Core\Database\ConnectionRepository;
use Rf\Core\Database\QueryEngine\Select;

/**
 * Trait ExecuteSelectTrait
 *
 * @package Rf\Core\Database\QueryEngine\Actions
 */
trait ExecuteSelectTrait {

    /**
     * Get the result of a query as an array or array of arrays
     *
     * @param bool $forceArray
     * @param string $mode
     *
     * @return array
     * @throws \Rf\Core\Exception\BaseException
     */
    public function toArray($forceArray = false, $mode = 'both') {

    	if(!empty($this->cached)) {

    		$key = 'query-' . md5($this->compile() . implode('', $this->generateValueArray()) . $forceArray . $mode);
    		$cachedValue = rf_cache_get($key);

    		if(isset($cachedValue) && $cachedValue !== false) {
    			return json_decode($cachedValue, true);
		    }

	    }

        $results =  $this->getConnection()->executeToArray($this->compile(), $this->generateValueArray(), $forceArray, $mode);

	    if(!empty($this->cached)) {
		    rf_cache_set($key, json_encode($results), $this->cachedExpires);
	    }

	    return $results;

    }

    /**
     * Get the result of a query as an associative array
     *
     * @param bool $forceArray
     *
     * @return array
     */
    public function toArrayAssoc($forceArray = false) {

        return $this->toArray($forceArray, 'assoc');

    }

    /**
     * Get the result of a query as an numeric array
     *
     * @param bool $forceArray
     *
     * @return array
     */
    public function toRow($forceArray = false) {

        return $this->toArray($forceArray, 'num');

    }

    /**
     * Get the result of a query as an object or array of objects. A class name can be specified
     * to map the result in an object of this class.
     *
     * @param null|string $className
     * @param bool $forceArray
     * @param array $options Options
     *
     * @return array|object
     * @throws \Rf\Core\Exception\BaseException
     */
    public function toObject($className = null, $forceArray = false, array $options = []) {

        return $this->getConnection()->executeToObject($this->compile(), $this->generateValueArray(), $className, $forceArray, $options);

    }

    //@TODO: toEntity function
	// Pass classname from entity to select and add Exception if empty

    /**
     * Get the number of rows for the current query
     *
     * @return int
     */
    public function toCount() {

        $baseQuery = clone $this;
        $baseQuery->orderBy('');
        $baseQuery->limit(0);

        if(!empty($this->groupByClause)) {

            $countQuery = new Select($baseQuery);
            $countQuery->setConnection($baseQuery->getConnection());
            $countQuery->fields(['COUNT(*) AS count']);
            $countQuery->valuesVal = $baseQuery->valuesVal;
            $countQuery->whereClauseValues = $baseQuery->whereClauseValues;
            $countQuery->havingClauseValues = $baseQuery->havingClauseValues;
            $countResult = $countQuery->toArrayAssoc();

        } else {

            $baseQuery->fields(['COUNT(*) AS count']);
            $countResult = $baseQuery->toArrayAssoc();

        }

        return !empty($countResult['count']) ? (int) $countResult['count'] : 0;

    }

}