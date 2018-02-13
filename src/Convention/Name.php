<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Convention;

/**
 * Class Name
 *
 * @since 1.0
 *
 * @package Rf\Core\Convention
 */
abstract class Name {

    /**
     * Transform a table name to a class name
     *
     * @param string $tableName Table name to transform
     *
     * @return string
     */
    public static function tableToClass($tableName) {

        $className = 'App\\Entities\\' . str_replace(' ', '', ucwords(str_replace('_', ' ', $tableName)));

        if(substr($tableName, 0, 4) === 'rf__') {
            $className = str_replace('Rf', 'Rf_', $className);
        }

        if(rf_config('orm.table-names-plural')) {

            if(strpos($tableName, 'systempay')) {
                return $className;
            } elseif(substr($className, -3, 3) === 'ies') {
                $className = substr($className, 0, -3) . 'y';
            } elseif(substr($className, -3, 3) === 'ses' || substr($className, -3, 3) === 'xes') {
                $className = substr($className, 0, -2);
            } elseif(substr($className, -3, 3) === 'tus') {
                $className = $className;
            } elseif(substr($className, -1, 1) === 's') {
                $className = substr($className, 0, -1);
            }

        }

        $className = str_replace('Oauth', 'OAuth', $className);

        return $className;

    }

    /**
     * Transform a class name to a table name
     *
     * @param string $className Class name to transform
     *
     * @return string
     */
    public static function classToTable($className) {

	    $classNameParts = explode('\\', $className);
	    $className = $classNameParts[count($classNameParts) -1];

	    if(strpos($className, 'Systempay')) {
		    $tableName = $className;
	    } elseif(strpos($className, 'OAuth') === 0) {
		    $tableName = $className;
	    } elseif(rf_config('orm.table-names-plural')) {
	        if(substr($className, -1, 1) == 'y') {
                $tableName = substr($className, 0, -1) . 'ies';
            } elseif(substr($className, -2, 2) == 'us') {
                $tableName = $className;
            } elseif(substr($className, -1, 1) == 's' || substr($className, -1, 1) == 'x') {
                $tableName = $className . 'es';
            } else {
                $tableName = $className . 's';
            }
        } else {
            $tableName = $className;
        }

        $tableName = lcfirst($tableName);
        $tableName = str_replace(
            array('A' ,'B' ,'C' ,'D' ,'E' ,'F' ,'G' ,'H' ,'I' ,'J' ,'K' ,'L' ,'M' ,'N' ,'O' ,'P' ,'Q' ,'R' ,'S' ,'T' ,'U' ,'V' ,'W' ,'X' ,'Y' ,'Z' ),
            array('_a','_b','_c','_d','_e','_f','_g','_h','_i','_j','_k','_l','_m','_n','_o','_p','_q','_r','_s','_t','_u','_v','_w','_x','_y','_z'),
            $tableName
        );

        $tableName = str_replace('o_auth', 'oauth', $tableName);

        return $tableName;

    }

    /**
     * Transform a field name to a property name
     *
     * @param string $fieldName Field name to transform
     *
     * @return string
     */
    public static function fieldToProperty($fieldName) {

        $propertyName = str_replace('_', ' ', $fieldName);
        $propertyName = ucwords($propertyName);
        $propertyName = str_replace(' ', '', $propertyName);
        $propertyName = lcfirst($propertyName);

        return $propertyName;

    }

    /**
     * Transform an array of field names to an array of property names
     *
     * @param array $a_fieldNames Array of field names to transform
     *
     * @return array
     */
    public static function fieldsToProperties($a_fieldNames) {

        $a_propertyNames = array();
        foreach ($a_fieldNames as $fieldName) {

            $propertyName = str_replace('_', " ", $fieldName);
            $propertyName = ucwords($propertyName);
            $propertyName = str_replace(" ", '', $propertyName);
            $propertyName = lcfirst($propertyName);
            array_push($a_propertyNames, $propertyName);

        }

        return $a_propertyNames;

    }

    /**
     * Transform a property name to a field name
     *
     * @param string $propertyName Property name to transform
     *
     * @return string
     */
    public static function propertyToField($propertyName) {

        return self::propertiesToFields($propertyName);

    }

    /**
     * Transform an array of property names to an array of field names
     *
     * @param string|array $propertyNames Array of property names to transform
     *
     * @return string|array
     */
    public static function propertiesToFields($propertyNames) {

        $fieldNames = str_replace(
            array('A' ,'B' ,'C' ,'D' ,'E' ,'F' ,'G' ,'H' ,'I' ,'J' ,'K' ,'L' ,'M' ,'N' ,'O' ,'P' ,'Q' ,'R' ,'S' ,'T' ,'U' ,'V' ,'W' ,'X' ,'Y' ,'Z' ),
            array('_a','_b','_c','_d','_e','_f','_g','_h','_i','_j','_k','_l','_m','_n','_o','_p','_q','_r','_s','_t','_u','_v','_w','_x','_y','_z'),
            $propertyNames
        );

        return $fieldNames;

    }

    /**
     * Transform a foreign key name to a class name
     *
     * @param string $fkName Foreign key name to transform
     *
     * @return string
     */
    public static function fkToClass($fkName) {

        $className = substr($fkName, 0, -3);
        $className = str_replace('_', ' ', $className);
        $className = ucwords($className);
        $className = str_replace(' ', '', $className);

        return 'App\\Entities\\' . $className;

    }

    /**
     * Transform a foreign key name to a table name
     *
     * @param string $fkName Foreign key name to transform
     *
     * @return string
     */
    public static function fkToTable($fkName) {

        $tableName = substr($fkName, 0, -3);
        $tableName = self::classToTable($tableName);

        return $tableName;

    }

    /**
     * Transform a table name to a foreign key name
     *
     * @param string $tableName Table name to transform
     *
     * @return string
     */
    public static function tableToFk($tableName) {

        if(substr($tableName, -3, 3) === 'ies') {
            return substr($tableName, 0, -3) . 'y' . '_id';
        } elseif(substr($tableName, -3, 3) === 'ses') {
            return substr($tableName, 0, -2) . '_id';
        } elseif(substr($tableName, -1, 1) === 's') {
            return substr($tableName, 0, -1) . '_id';
        } else {
            return $tableName . '_id';
        }

    }

    /**
     * Transform a controller name to a module name
     *
     * @param string $controllerName Controller name to transform
     *
     * @return string
     */
    public static function controllerToModule($controllerName) {

        $moduleName = explode('Controller', $controllerName);
        $moduleName = strtolower($moduleName[0]);

        return $moduleName;

    }

    /**
     * Transform a module name to a controller name
     *
     * @param string $moduleName Module name to transform
     *
     * @return string
     */
    public static function moduleToController($moduleName) {

        $controllerName = str_replace(' ', '', ucfirst(str_replace('_', ' ', $moduleName)));

        return $controllerName . 'Controller';

    }
}