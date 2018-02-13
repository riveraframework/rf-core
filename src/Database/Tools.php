<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Database;

use Rf\Core\Database\QueryEngine\Describe;
use Rf\Core\Database\QueryEngine\Select;

/**
 * Class Tools
 * Libraries containing useful function relatives to the database
 *
 * @package Rf\Core\Database
 */
abstract class Tools {
    
    /**
     * Get table field names and return it as an array
     *
     * @param string $tableName Table name
     * @param string $database Database name
     *
     * @return array 
     */
    public static function getTableFieldNames($tableName, $database = null) {

        $query = new Describe($tableName);
        if(isset($database)) {
            $query->database($database);
        }

        $result = $query->toArray(true);

        $a_fields = array();
        for($i = 0 ; $i < count($result) ; $i++) {
            array_push($a_fields, $result[$i]['Field']);
        }

        return $a_fields;
    }
    
    /**
     * Check if a table exists
     *
     * @param string $tableName Table name
     * @param string $database Database name
     *
     * @return bool
     */
    public static function tableExist($tableName, $database = null) {

        if(!isset($database)) $database = rf_config('database.name');

        $query = new Select('TABLES', 'information_schema');
        $query->fields(['TABLE_NAME', 'TABLE_SCHEMA']);
        $query->whereEqual('TABLE_NAME', $tableName);
        $query->whereAnd();
        $query->whereEqual('TABLE_SCHEMA', $database);

        $result = $query->toArrayAssoc(true);

        return count($result) > 0 ? true : false;
    }
    
    /**
     * Get the row count in a table for a field name/value pair
     *
     * @param string $field Field name
     * @param string $value Field value
     * @param string $tableName Table name
     *
     * @return int|false
     */
    public static function rowCount($field, $value, $tableName) {

        $query = new Select($tableName);
        $query->fields(['COUNT(*) AS count']);
        $query->whereEqual($field, $value);

        $result = $query->toArrayAssoc();

        return isset($result['count']) && is_numeric($result['count']) ? (int) $result['count'] : false;
    }
}