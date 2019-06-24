<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Database\MySQL;

/**
 * Class MySQLTools
 *
 * @package Rf\Core\Database\MySQL
 */
trait MySQLTools {

    /**
     * Get the table list for a database
     *
     * @param string|null $databaseName
     * @return array
     *
     * @throws \Exception
     */
    public function getTableList($databaseName = null) {

        // Use the default database name if none is provided
        if(!isset($databaseName)) {
            $databaseName = $this->getCurrentDatabase();
        }

        /** @var MySQLQueryBuilder $qb */
        $qb = $this->getQueryBuilder();
        $query = $qb->select('TABLES', 'information_schema');
        $query->fields(['TABLE_NAME', 'TABLE_SCHEMA']);
        $query->whereEqual('TABLE_SCHEMA', $databaseName);

        return $query->toArrayAssoc(true);

    }
    
    /**
     * Get table fields and return it as an array
     *
     * @param string $tableName Table name
     * @param string $databaseName Database name
     *
     * @return array
     * @throws \Exception
     */
    public function getTableStructure($tableName, $databaseName = null) {

        // Use the default database name if none is provided
        if(!isset($databaseName)) {
            $databaseName = $this->getCurrentDatabase();
        }

        /** @var MySQLQueryBuilder $qb */
        $qb = $this->getQueryBuilder();
        $query = $qb->describe($tableName, $databaseName);

        $fields = $query->toArrayAssoc(true);

        return $fields;

    }

    /**
     * Get table field names and return it as an array
     *
     * @param string $tableName Table name
     * @param string $databaseName Database name
     *
     * @return array
     * @throws \Exception
     */
    public function getTableFieldNames($tableName, $databaseName = null) {

        $fields = $this->getTableStructure($tableName, $databaseName);

        $fieldNames = [];
        for($i = 0; $i < count($fields); $i++) {

            $fieldNames[] = $fields[$i]['Field'];

        }

        return $fieldNames;

    }
    
    /**
     * Check if a table exists
     *
     * @param string $tableName Table name
     * @param string $databaseName Database name
     *
     * @return bool
     * @throws \Exception
     */
    public function tableExist($tableName, $databaseName = null) {

        // Use the default database name if none is provided
        if(!isset($databaseName)) {
            $databaseName = $this->getCurrentDatabase();
        }

        /** @var MySQLQueryBuilder $qb */
        $qb = $this->getQueryBuilder();
        $query = $qb->select('TABLES', 'information_schema');
        $query->fields(['TABLE_NAME', 'TABLE_SCHEMA']);
        $query->whereEqual('TABLE_NAME', $tableName);
        $query->whereAnd();
        $query->whereEqual('TABLE_SCHEMA', $databaseName);

        $result = $query->toArrayAssoc(true);

        return count($result) > 0;

    }

}