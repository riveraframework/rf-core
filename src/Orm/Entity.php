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
 * @TODO: refaire le mapper d'objets avec DATETIME qui génère direct un objet date?
 * @TODO: WhereClause commune aux traitements
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

    /**
     * @var Entity $backup Clone of the entity at his initial state (new|get)
     */
    protected $backup;

    /** @var array $dbStructure Table structure in database */
    protected static $dbStructure;

    /** @var array $dbStructureExceptions */
    protected static $dbStructureExceptions = [];

    /**
     * Create an entity
     *
     * @param bool $backup
     *
     * @TODO: mettre les entités en cache dans des fichiers XML ? /entities/db
     */
    public function __construct($backup = true) {

        // Save the initial state in backup variable
        if($backup) {
            $this->createBackup();
        }

    }

    public function getId() {

        return $this->id;

    }

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
     * @throws DebugException
     */
    public function setBackup($entity = null) {

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
     */
    public function createBackup() {

        $this->backup = clone $this;
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
     *
     * @param $tableName
     * @param $colName
     *
     * @return bool
     */
    public static function isDbStructureException($tableName, $colName) {

        return isset(Entity::$dbStructureExceptions[$tableName][$colName]) ? true : false;

    }

    /**
     *
     * @param $tableName
     * @param $colName
     *
     * @return array
     */
    public static function getDbStructureException($tableName, $colName) {

        return isset(Entity::$dbStructureExceptions[$tableName][$colName]) ? Entity::$dbStructureExceptions[$tableName][$colName] : [];

    }

    /**
     *
     * @param $tableName
     * @param $colName
     * @param $field
     * @param $value
     */
    public static function setDbStructureException($tableName, $colName, $field, $value) {

        Entity::$dbStructureExceptions[$tableName][$colName][$field] = $value;

    }

    /**
     * Check if the property has a default value
     *
     * @param string $field Property name
     *
     * @return bool
     */
    private function hasDefaultValue($field) {

        return $this->getDbStructure($field, 'Default') != null ? true : false;

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

        return $this->getDbStructure($field, 'Null') == 'NO' ? true : false;

    }

    /**
     * Check is the property is a primary key
     *
     * @param string $field Property name
     *
     * @return bool
     */
    protected function isPrimaryKey($field) {

        return $this->getDbStructure($field, 'Key') == 'PRI' ? true : false;

    }

    /**
     * Check is the property is a foreign key
     *
     * @param string $field Property name
     *
     * @return bool
     */
    private function isForeignKey($field) {

        return $this->getDbStructure($field, 'Key')  == 'FK' ? true : false;

    }

    /**
     * Check is the property is a foreign key
     *
     * @param string $fieldName Property name
     *
     * @return bool
     */
    public static function isForeignKeyNew($fieldName) {

        if(strpos($fieldName, '_id') && class_exists(Name::fkToClass($fieldName))) {

            return true;

        }

        return false;

    }

    /**
     * Unset a property
     *
     * @param string $propertyName Property name
     *
     * @return void
     */
    public function unsetProperty($propertyName) {

        unset($this->{$propertyName});

    }

    /**
     * Get the primary key(s)
     *
     * @return int|array
     */
    public function getPrimaryKeys() {

        $pri = [];

        foreach(get_object_vars($this) as $key => $value) {

            if($this->isPrimaryKey(Name::propertyToField($key))) {
                $pri[$key] = $value;
                $last = $key;
            }

        }

        return count($pri) === 1 ? $pri[$last] : $pri;

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
     * Cette fonction retourne une chaine de caractère contenant la
     * liste des champs de l'objet passé en paramètre et une seconde
     * contenant la liste des valeurs correspondantes.
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
            // @TODO: gestion INT = 0
            if(
                !isset($vars[$key])
                || is_object($vars[$key])
                || (!is_object($vars[$key]) && is_object($this->backup->$key))
                || $vars[$key] != $this->backup->$key
                || (!$vars[$key] && !isset($this->backup->$key))
            ) {

                if(is_object($vars[$key])) {

                    // Si son ID est null on commence par l'enregistrer en base.
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
     */
    private function addEntity($forceId = false) {

        // On récupère les champs et les valeurs à insérer
        $params = $this->getParamsForSave($forceId);

        // On prépare la requète
        $query = new Insert(Name::classToTable(get_class($this)));
        $query->setConnection(ConnectionRepository::getConnection(static::conn_name));
        $query->fields($params['fields']);
        $query->values($params['values']);

        // On l'exécute et on met à jour l'ID de l'entity avec celui qui vient d'être inséré
        if(property_exists(get_class($this), 'id') && $forceId !== true) {
            return $this->id = $query->addAndGetId();
        } else {
            return $query->execute($query);
        }

    }

    /**
     * Cette fonction de modifier automatiquement une entité.
     *
     * @param bool $forceId
     *
     * @return int
     *
     * @TODO: Sauver les objets en chaines !!
     */
    private function updateEntity($forceId = false) {

        // On récupère les champs et les valeurs à modifier
        $params = $this->getParamsForSave($forceId);

        if(count($params['fields']) > 0) {

            $query = new Update(Name::classToTable(get_class($this)));
            $query->setConnection(ConnectionRepository::getConnection(static::conn_name));
            $query->fields($params['fields']);
            $query->values($params['values']);
            $uk = $this->getPrimaryKeys();

            if(!is_array($uk)) {

                if(is_numeric($uk)) {
                    $query->addWhereClauseEqual('id', $uk);
                } elseif(!is_numeric($uk) && property_exists(get_class($this), 'username')) {
                    $query->addWhereClauseEqual('username', $uk);
                } elseif(!is_numeric($uk) && property_exists(get_class($this), 'slug')) {
                    $query->addWhereClauseEqual('slug', $uk);
                }

            } else {

                $count = 1;
                foreach($uk as $property => $value) {

                    if(property_exists(get_class($this), $property)) {
                        $count == 1
                            ? $query->addWhereClauseEqual(Name::propertyToField($property), $value)
                            : $query->addWhereClauseAndEqual(Name::propertyToField($property), $value);
                        $count++;
                    }

                }
            }

            return $query->execute();
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
     * Cette fonction permet de déterminer si on est en train de tenter d'ajouter un
     * nouvel objet ou si l'on tente de modifier un existant.
     *
     * @TODO: Add a flag when entity is retrieved from DB
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

        } catch(DebugException $e) {
            throw new DebugException(Log::TYPE_ERROR, 'Impossible de sauver l\'entité (' . get_class($this) . ') :' . $e->getMessage());
        }

    }

    // @TODO: saveField ????

    /**
     * Delete an entity in database
     *
     * @throws DebugException
     */
    public function remove() {

        // Create query
        $deleteQuery = static::delete();

        if(property_exists($this, 'id')) {

            // Default process (primary key = id)
            if(!isset($this->id)) {
                throw new DebugException(Log::TYPE_ERROR, 'Erreur de clé primaire (id)');
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
            throw new DebugException(Log::TYPE_ERROR, 'Erreur de clé primaire');
        }

        // Execute query
        $deleteQuery->execute();

    }

    /**
     * Cette fonction permet de supprimer l'entité cible. Il est possible de passer
     * un tableau de clés primaires par la variable $uk.
     *
     * @param string $className
     * @param mixed $uk
     */
    public static function deleteEntity($className, $uk) {

        /** @var Entity $object */
        $object = new $className();

        if(!is_array($uk)) {

            if(is_numeric($uk)) {
                $object->id = $uk;
            } elseif(!is_numeric($uk) && property_exists($className, 'slug')) {
                $object->slug = $uk;
            }

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
        $this->backup = clone new static(null, 'default', false);

    }

    /**
     * Remove the ID and the backup of an entity
     *
     * @param Entity $entity
     *
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
     */
    public static function exists($value, $propertyName = 'id') {

        $count = static::select()
            ->whereEqual($propertyName, $value)
            ->toCount();

        return $count > 0 ? true : false;

    }

    /**
     * Select in the current entity table
     *
     * @param string $alias
     *
     * @return Select
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
     */
    public static function delete($alias = '') {

        $delete = new Delete(static::table_name . ($alias != '' ? ' AS ' . $alias : ''));
        $delete->setConnection(static::getConnection());

        return $delete;

    }

}