<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Orm;

use Rf\Core\Base\Date;
use Rf\Core\Base\Exceptions\DebugException;
use Rf\Core\Database\PDO;
use Rf\Core\Log\Log;
use Rf\Core\Utils\Format\Name;
use Rf\Core\Database\ConnectionRepository;
use Rf\Core\Database\QueryEngine\Delete;
use Rf\Core\Database\QueryEngine\Insert;
use Rf\Core\Database\QueryEngine\Select;
use Rf\Core\Database\QueryEngine\Update;

/**
 * Class Entity
 *
 * @package Rf\Core\Orm
 *
 * @TODO: Option to retrieve DATETIME as Date objects
 *
 */
abstract class Entity {

    /** @var string Connection name */
    const conn_name = '';

    /** @var string Table name */
    const table_name = '';

    /** @var array Table structure */
    const table_structure = [];

    /** @var int */
    protected $id;

    /** @var Entity $backup Clone of the entity at his initial state (new|get) */
    protected $backup;

    /** @var array $dbStructure Table structure in database */
    protected static $dbStructure;

    /**
     * Create an entity
     *
     * @param bool $backup
     * @throws \Exception
     */
    public function __construct($backup = true) {

        // Save the initial state in backup variable
        if($backup) {
            $this->createBackup();
        }

    }

    /**
     * Get the entity ID
     *
     * @return int
     */
    public function getId() {

        return $this->id;

    }

    /**
     * Set the entity ID
     *
     * @param int $id
     */
    public function setId($id) {

        $this->id = $id;

    }

    /**
     * Get the backup entity
     *
     * @return static
     */
    public function getBackup() {

        return $this->backup;

    }

    /**
     * Set the backup entity
     *
     * @param Entity $entity
     *
     * @throws \Exception
     */
    public function setBackup(Entity $entity = null) {

        if(is_null($entity)) {

            $this->backup = null;

        } elseif(!is_a($entity, static::class)) {

            throw new \Exception('Cannot set a backup of a different entity');

        } else {

            $this->backup = clone $entity;
            $this->backup->setBackup(null);

        }

    }

    /**
     * Create the backup entity
     *
     * @throws \Exception
     */
    public function createBackup() {

        // Create the backup by cloning the current object
        $this->backup = clone $this;

        // Remove the clone's backup
        $this->backup->setBackup(null);

    }

    /**
     * Get table name
     *
     * @return string
     */
    public static function getTableName() {

        return static::table_name;

    }

    /**
     * Get connection
     *
     * @return PDO
     * @throws \Exception
     */
    public static function getConnection() {

        return ConnectionRepository::getConnection(static::conn_name);

    }

    /**
     * Get database structure
     *
     * @param null|string $field
     * @param null|string $data
     *
     * @return mixed
     */
    public function getDbStructure($field = null, $data = null) {

        if(!isset($field)) {

            return static::table_structure;

        } elseif(in_array($field, array_keys(static::table_structure))) {

            if(in_array($data, array_keys(static::table_structure[$field]))) {
                return static::table_structure[$field][$data];
            } else {
                return static::table_structure[$field];
            }

        } else {
            return static::table_structure;
        }

    }

    /**
     * Check if the property has a default value
     *
     * @param string $field Property name
     *
     * @return bool
     */
    private function hasDefaultValue($field) {

        return $this->getDbStructure($field, 'Default') != null;

    }

    /**
     * Get the property's default value
     *
     * @param string $field Property name
     *
     * @return mixed
     */
    private function defaultValue($field) {

        return $this->getDbStructure($field, 'Default');

    }

    /**
     * Check is the property can be null
     *
     * @param string $field Property name
     *
     * @return bool
     */
    private function isNotNull($field) {

        return $this->getDbStructure($field, 'Null') == 'NO';

    }

    /**
     * Check is the property is a primary key
     *
     * @param string $field Property name
     *
     * @return bool
     */
    protected function isPrimaryKey($field) {

        return $this->getDbStructure($field, 'Key') == 'PRI';

    }

    /**
     * Check is the property is a foreign key
     *
     * @param string $field Property name
     *
     * @return bool
     */
    protected function isForeignKey($field) {

        return $this->getDbStructure($field, 'Key')  == 'FK';

    }

    /**
     * Check is the property is a foreign key
     *
     * @param string $fieldName Property name
     *
     * @return bool
     */
    public static function isForeignKeyNew($fieldName) {

        return strpos($fieldName, '_id') && class_exists(Name::fkToClass($fieldName));

    }

    /**
     * Unset a property
     *
     * @param string $propertyName Property name
     */
    public function unsetProperty($propertyName) {

        unset($this->{$propertyName});

    }

    /**
     * Get the primary key(s), indexed by field name
     *
     * @return int|array
     */
    public function getPrimaryKeys() {

        $pks = [];

        foreach(get_object_vars($this) as $key => $value) {

            if($this->isPrimaryKey(Name::propertyToField($key))) {
                $pks[$key] = $value;
            }

        }

        return $pks;

    }

    /**
     * Get the public fields
     *
     * @return mixed
     */
    private function getPublicFields() {

        $getPublicFields = function($obj) { return get_object_vars($obj); };

        return $getPublicFields($this);

    }

    /**
     * Get an array of the params tu update during save
     *
     * @param bool $forceId (default:false)
     *
     * @return array
     */
    private function getParamsForSave($forceId = false) {

        // Get list of field to check
        $vars = $this->getPublicFields();

        // Remove the ID except if we want to force it
        if(property_exists(get_class($this), 'id') && $forceId !== true) {
            unset($vars['id']);
        }

        // Init return table
        $params = ['fields' => [], 'values' => []];

        // Process every object properties
        foreach(array_keys($vars) as $key) {

            if(!in_array($key, array_keys(static::table_structure))) {
                continue;
            }

            $object = false;
            // @TODO: case where INT = 0
            if(
                !isset($vars[$key])
                || is_object($vars[$key])
                || (!is_object($vars[$key]) && is_object($this->backup->$key))
                || $vars[$key] != $this->backup->$key
                || (!$vars[$key] && !isset($this->backup->$key))
            ) {

                if(is_object($vars[$key])) {

                    // Save in database if ID is null
                    if(is_a($vars[$key], Date::class)) {
                        $vars[$key] = $vars[$key]->format('sql');
                    } elseif(!isset($vars[$key]->id)) {
                        $vars[$key]->save();
                        $vars[$key] = $vars[$key]->id;
                    } else {
                        $object = true;
                    }
                }

                if(isset($vars[$key]) && $object === false) {

                    array_push($params['fields'], Name::propertyToField($key));

                    if($vars[$key] === false) {
                        array_push($params['values'], 0);
                    } elseif($vars[$key] === true) {
                        array_push($params['values'], 1);
                    } else {
                        array_push($params['values'], $vars[$key]);
                    }

                } else {

                    if($object === true || ($this->isNotNull($key) === false && $vars[$key] == $this->backup->$key)) {
                        // Do nothing
                    } elseif($this->isNotNull($key) === true  && $this->hasDefaultValue($key) === true) {
                        array_push($params['fields'], Name::propertyToField($key));
                        array_push($params['values'], $this->defaultValue($key));
                    } else {
                        array_push($params['fields'], Name::propertyToField($key));
                        array_push($params['values'], null);
                    }

                }
            }
        }

        return $params;

    }

    /**
     * Add an entity in database
     *
     * @param bool $forceId
     *
     * @return int|string
     * @throws \Exception
     */
    private function addEntity($forceId = false) {

        // Get fields and values to insert
        $params = $this->getParamsForSave($forceId);

        // Prepare query
        $query = new Insert(Name::classToTable(get_class($this)));
        $query->setConnection(ConnectionRepository::getConnection(static::conn_name));
        $query->fields($params['fields']);
        $query->values($params['values']);

        // Execute query and update the ID in the current object
        if(property_exists(get_class($this), 'id') && $forceId !== true) {
            return $this->id = $query->addAndGetId();
        } else {
            return $query->execute();
        }

    }

    /**
     * Update an entity in database
     *
     * @param bool $forceId
     *
     * @return int|void
     * @throws \Exception
     *
     * @TODO: Update objects recursively
     */
    private function updateEntity($forceId = false) {

        // Get fields and values to update
        $params = $this->getParamsForSave($forceId);

        if(count($params['fields']) > 0) {

            $query = new Update(Name::classToTable(get_class($this)));
            $query->setConnection(ConnectionRepository::getConnection(static::conn_name));
            $query->fields($params['fields']);
            $query->values($params['values']);
            $uk = $this->getPrimaryKeys();

            foreach($uk as $property => $value) {

                if(property_exists(get_class($this), $property)) {
                    $query->whereAnd();
                    $query->whereEqual(Name::propertyToField($property), $value);
                }

            }

            return $query->execute();

        } else {
            // No need to update
            return;
        }

    }

    /**
     * Get the first entity matching the given criteria
     *
     * @param string $where
     * @param array $options
     *
     * @return null|static
     */
    public static function findFirstBy($where, array $options = []) {

        $options['limit'] = 1;

        $results = static::findBy($where, $options);

        if(!empty($results)) {
            return $results[0];
        } else {
            return null;
        }

    }

    /**
     * Get entities by criteria
     *
     * @param string $where
     * @param array $options
     *
     * @return static[]
     */
    public static function findBy($where = '', array $options = []) {

        return static::findByEngine($where, $options);

    }

    /**
     * Internal engine to get entities
     *
     * @param string $where
     * @param array $options [limit => null, offset => null, depth => null]
     *
     * @return static[]
     */
    protected static function findByEngine($where, array $options) {

        // Build query
        $getEntities = static::select();
        $getEntities->where($where);

        // Set query options
        if(isset($options['orderby'])) {
            $getEntities->orderBy($options['orderby']);
        }

        // Set query options
        if(isset($options['limit'])) {
            if(isset($options['offset'])) {
                $getEntities->limit($options['offset'], $options['limit']);
            } else {
                $getEntities->limit($options['limit']);
            }
        }

        // Get results
        /** @var static[] $entities */
        $entities = $getEntities->toObject(static::class, true, $options);

        // Process entities
        foreach($entities as &$entity) {

            // Create the backup entity used during save
            $entity->createBackup();

            // Load other levels if depth is set and > 0
            if(!empty($options['depth'])) {

                $fields = array_keys(static::table_structure);

                foreach($fields as $fieldName) {

                    // Check if this field is a FK
                    if(static::isForeignKeyNew($fieldName)) {

                        // Get the method names
                        $propertyName = Name::fieldToProperty($fieldName);
                        $methodGetName = 'get' . ucwords($propertyName);
                        $methodSetName = 'set' . ucwords($propertyName);

                        // Get the current FK value
                        if(method_exists($entity, $methodGetName)) {
                            $value = $entity->$methodGetName();
                        } else {
                            $value = $entity->$propertyName;
                        }

                        // Decrement the depth count for the new query
                        $newOptions = $options;
                        $newOptions['depth']--;

                        // Get the referenced entity
                        $referencedEntity = static::findFirstBy('id = ' . $value, $newOptions);

                        // Set the new value in the entity field
                        // @TODO: Set in a new property to keep the FK ref?
                        if(method_exists($entity, $methodSetName)) {
                            $entity->$methodSetName($referencedEntity);
                        } else {
                            $entity->$propertyName = $referencedEntity;
                        }

                    }

                }

            }
        }

        return $entities;

    }

    /**
     * Save the current entity in the database
     * Automatically detect if the entity needs to be added or updated.
     *
     * @TODO: Add a flag when entity is retrieved from DB
     *
     * @throws DebugException
     */
    public function save() {

        try {

            $exist = !is_numeric($this->id) ? false : static::exists($this->id);

            if(is_numeric($this->id) && $this->id > 0 && $exist === true) {
                $this->updateEntity();
            } elseif(is_numeric($this->id) && $this->id > 0 && $exist === false) {
                $this->addEntity(true);
            } else {
                $this->addEntity();
            }

            $this->backup = clone $this;

        } catch(\Exception $e) {
            throw new DebugException(Log::TYPE_ERROR, 'Unable to save entity (' . self::class . ') :' . $e->getMessage());
        }

    }

    // @TODO: saveField ????

    /**
     * Delete an entity in database
     *
     * @throws \Exception
     * @throws DebugException
     */
    public function remove() {

        // Create query
        $deleteQuery = static::delete();

        if(property_exists($this, 'id')) {

            // Default process (primary key = id)
            if(!isset($this->id)) {
                throw new DebugException(Log::TYPE_ERROR, 'Primary key error (id)');
            }

            $deleteQuery->whereEqual('id', $this->id);

        } else {

            // Other primary keys types
            $count = 1;
            foreach(get_object_vars($this) as $property => $value) {

                $field = Name::propertyToField($property);

                if($this->isPrimaryKey($field)) {

                    $function = 'get' . ucfirst($property);

                    $deleteQuery->whereAnd();
                    $deleteQuery->whereEqual($field, $this->$function());

                    $count++;

                }

            }

        }

        // Throw an exception if the where clause is empty
        if(empty($deleteQuery->whereClause)) {
            throw new DebugException(Log::TYPE_ERROR, 'Primary key error');
        }

        // Execute query
        $deleteQuery->execute();

    }

    /**
     * Delete an entity
     *
     * @TODO: Replace by a delete query
     *
     * @param string $className
     * @param mixed $uk
     *
     * @throws \Exception
     */
    public static function deleteEntity($className, $uk) {

        /** @var Entity $object */
        $object = new $className();

        if(!is_array($uk)) {

            $object->id = $uk;

        } else {

            foreach($uk as $key => $value) {
                $object->$key = $value;
            }

        }

        $object->remove();

    }

    /**
     * Remove the ID and the backup of an entity
     */
    public function unsigned() {

        $this->id = null;
        $this->backup = clone new static(false);

    }

    /**
     * Remove the ID and the backup of an entity
     *
     * @param Entity $entity
     *
     * @throws \Exception
     * @throws DebugException
     */
    public function replace($entity) {

        if(!is_a($entity, static::class)) {
            throw new DebugException(Log::TYPE_ERROR, 'Cannot replace and entity with a different type of entity');
        }

        $entity->setId($this->id);
        $entity->setBackup($this);
        $entity->getBackup()->setBackup(null);
        $entity->save();

    }

    /**
     * Get the current instance as an array
     *
     * @param array $columns
     *
     * @return array
     */
    public function toArray(array $columns = []) {

        $array = get_object_vars($this);

        foreach($array as $key => $var) {
            if(
                !in_array($key, array_keys(static::table_structure))
                || (!empty($columns) && !in_array($key, $columns))
            ) {
                unset($array[$key]);
            }
        }

        return $array;

    }

    /**
     * Check if an entity exist
     *
     * @param mixed $value
     * @param string $propertyName
     *
     * @return bool
     * @throws \Exception
     */
    public static function exists($value, $propertyName = 'id') {

        $count = static::select()
            ->whereEqual($propertyName, $value)
            ->toCount();

        return $count > 0;

    }

    /**
     * Select in the current entity table
     *
     * @param string $alias
     *
     * @return Select
     * @throws \Exception
     */
    public static function select($alias = '') {

        $select = new Select(static::table_name . ($alias != '' ? ' AS ' . $alias : ''));
        $select->setConnection(static::getConnection());
        $select->setFetchEntity(static::class);

        return $select;

    }

    /**
     * Update in the current entity table
     *
     * @param string $alias
     *
     * @return Update
     * @throws \Exception
     */
    public static function update($alias = '') {

        $update = new Update(static::table_name . ($alias != '' ? ' AS ' . $alias : ''));
        $update->setConnection(static::getConnection());

        return $update;

    }

    /**
     * Delete in the current entity table
     *
     * @param string $alias
     *
     * @return Delete
     * @throws \Exception
     */
    public static function delete($alias = '') {

        $delete = new Delete(static::table_name . ($alias != '' ? ' AS ' . $alias : ''));
        $delete->setConnection(static::getConnection());

        return $delete;

    }

}