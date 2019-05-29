<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Utils\Format;

/**
 * Class Name
 *
 * @package Rf\Core\Utils\Format
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

        return StringTransform::toPascalCase($tableName);

    }

    /**
     * Transform a class name to a table name
     *
     * @param string $className Class name to transform
     *
     * @return string
     */
    public static function classToTable($className, $useFullNs = false) {

        $classNameParts = explode('\\', $className);

        if($useFullNs) {
            $tableName = implode('', $classNameParts);
        } else {
            $tableName = $classNameParts[count($classNameParts) -1];
        }

        return StringTransform::toSnakeCase($tableName);

    }

    /**
     * Transform a field name to a property name
     *
     * @param string $fieldName Field name to transform
     *
     * @return string
     */
    public static function fieldToProperty($fieldName) {

        return StringTransform::toCamelCase($fieldName);

    }

    /**
     * Transform an array of field names to an array of property names
     *
     * @param array $fieldNames Array of field names to transform
     *
     * @return array
     */
    public static function fieldsToProperties(array $fieldNames) {

        $propertyNames = [];
        foreach ($fieldNames as $fieldName) {

            $propertyNames[] = StringTransform::toCamelCase($fieldName);

        }

        return $propertyNames;

    }

    /**
     * Transform a property name to a field name
     *
     * @param string $propertyName Property name to transform
     *
     * @return string
     */
    public static function propertyToField($propertyName) {

        return StringTransform::toSnakeCase($propertyName);

    }

    /**
     * Transform an array of property names to an array of field names
     *
     * @param array $propertyNames Array of property names to transform
     *
     * @return array
     */
    public static function propertiesToFields($propertyNames) {

        $fieldNames = [];
        foreach ($propertyNames as $propertyName) {

            $fieldNames[] = StringTransform::toSnakeCase($propertyName);

        }

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
        $className = StringTransform::toPascalCase($className);

        return $className;

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
        $tableName = StringTransform::classToTable($tableName);

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

        return $tableName . '_id';

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

        $controllerName = StringTransform::toPascalCase($moduleName);

        return $controllerName . 'Controller';

    }
}