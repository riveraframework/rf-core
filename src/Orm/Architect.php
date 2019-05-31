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

use Rf\Core\Base\Exceptions\DebugException;
use Rf\Core\Log\Log;
use Rf\Core\Utils\Format\Name;
use Rf\Core\Database\ConnectionRepository;
use Rf\Core\Database\Query;
use Rf\Core\Database\Tools as DatabaseTools;

/**
 * Class Architect
 *
 * @package Rf\Core\Orm
 */
class Architect {

    /**
     * @var string $indentation Indentation character
     */
    protected $indentation = "\t";

    /**
     * @var array $history History of the operations executed by the architect
     */
    protected $history = [];

    /**
     * @var array $tableList Available table list
     */
    protected $currentTableList = [];

    /**
     * @var array $fileList Entities file list
     */
    protected $currentFileList = [];

    /**
     * Add a given number of tabulation using the indentation character
     *
     * @param int $multiplier Number of tabulations
     *
     * @return string
     */
    protected function tab($multiplier = 1) {

        return str_repeat($this->indentation, $multiplier);

    }
    
    /**
     * Update all entity files using the tables
     *
     * @return bool
     */
    public function refresh($connectionNames = []) {

	    try {

	        $connections = [];

            if(empty($connectionNames)) {

                $connection = ConnectionRepository::getDefaultConnection();
                $connections[] = $connection;
                $connectionNames[] = $connection->getName();

            } else {

                foreach($connectionNames as $connectionName) {
                    $connections[] = ConnectionRepository::getConnection($connectionName);
                }

            }

            // Create the model folder if missing
            foreach($connectionNames as $connectionName) {

                if(!is_dir(rf_dir('entities') . '/models/c_' . $connectionName)) {
                    mkdir(rf_dir('entities') . '/models/c_' . $connectionName, 0755, true);
                }

            }

            foreach($connections as $connection) {

                // Get table list
                $this->currentTableList = $connection->getTableList($connection->getCurrentDatabase());

                if(isset($this->currentTableList) && count($this->currentTableList) > 0) {
                    $this->history[] = '<span style="color:green;">La structure de la base de données a été récupérée avec succès.</span><br/><br/>';
                } elseif(isset($this->currentTableList) && count($this->currentTableList) == 0) {
                    $this->history[] = '<span style="color:green;">La structure de la base de données a été récupérée avec succès mais est vide.</span><br/><br/>';
                } else {
                    throw new DebugException(Log::TYPE_ERROR, 'Erreur dans la récupération de la structure de la base de données.');
                }

                // Update the entity file for each table
                foreach($this->currentTableList as $table) {
                    $this->refreshClassFile($connection->getName(), $table);
                }

                // Delete unused entity files
                array_unique($this->currentFileList);
                $this->cleanEntitiesDirectory($connection->getName(), $this->currentFileList);

            }

		    return true;

	    } catch(DebugException $e) {

		    $this->history[] =  '<span style="color:red;">' . $e->getMessage() . '</span><br/>';

		    return false;

	    }

    }

    /**
     * Get or display the architect history
     *
     * @param bool $echo false: return (default)|true: display
     * @return array
     */
    public function history($echo = false) {

        if($echo === true) {
            echo $this->history;
        } else {
            return $this->history;
        }

    }

    /**
     * Generate the entity file
     *
     * @param string $connName
     * @param string $table
     *
     * @TODO: Add phpDoc to generated files
     */
    private function refreshClassFile($connName, $table) {

        // Get table schema
        $structure = $this->getStructure($table['TABLE_NAME'], $table['TABLE_SCHEMA']);

        // Get class structure
        $className = Name::tableToClass($table['TABLE_NAME']);
	    $classNameWithoutNsParts = explode('\\', $className);
	    $classNameWithoutNs = $classNameWithoutNsParts[count($classNameWithoutNsParts) - 1];
	    $fieldNames = DatabaseTools::getTableFieldNames($table['TABLE_NAME'], $table['TABLE_SCHEMA']);
        $propertyNames = Name::fieldsToProperties($fieldNames);

        $this->history[] =  'Structure de la classe ' . ucfirst($classNameWithoutNs) . ' découpée avec succès.<br/>';

        // Create the file
        $fileName = $classNameWithoutNs . 'Model.php';
        $filePath = rf_dir('entities') . '/models/c_' . $connName . '/' . $fileName;
        array_push($this->currentFileList, $fileName);
        $fileOpen = fopen($filePath, 'w');

        /* ########################### CLASS START ################################ */

        $fileContent  = '/* ########################################################################## *' . PHP_EOL;
        $fileContent .= ' * #####################   Auto Generated File    ########################### *' . PHP_EOL;
        $fileContent .= ' * #####################      DO NOT MODIFY       ########################### *' . PHP_EOL;
        $fileContent .= ' * ########################################################################## */' . PHP_EOL;
        $fileContent .= PHP_EOL;
        $fileContent .= 'namespace App\\Entities\\Models\\C_' . $connName . ';' . PHP_EOL;
        $fileContent .= PHP_EOL;
        $fileContent .= 'use Rf\Core\\Orm\\Entity;' . PHP_EOL;
        $fileContent .= PHP_EOL;

        $fileContent .= 'abstract class ' . $classNameWithoutNs . 'Model extends Entity {' . PHP_EOL;
        $fileContent .= PHP_EOL;

	        // Constants
	        $fileContent .= $this->tab() . '// Constants' . PHP_EOL;
	        $fileContent .= $this->tab() . 'const conn_name = \'' . $connName . '\';' . PHP_EOL;
	        $fileContent .= $this->tab() . 'const table_name = \'' . $table['TABLE_NAME'] . '\';' . PHP_EOL;
		    $fileContent .= $this->tab() . 'const table_structure = ' . $structure . ';' . PHP_EOL;
		    $fileContent .= PHP_EOL;

            // Properties
            $fileContent .= $this->tab() . '// Properties' . PHP_EOL;
            foreach($fieldNames as $fieldName) {
                $fileContent .= $this->tab() . 'protected $' . $fieldName . ';' .PHP_EOL;
            }
	        $fileContent .= PHP_EOL;

            // Default getters and setters
            $fileContent .= $this->tab() . '// Getters and setters' . PHP_EOL;

            foreach ($propertyNames as $index => $propertyName) {

                // Getters
                $fileContent .= $this->tab() . 'public function get' . ucfirst($propertyName) . '() {' . PHP_EOL;
                    $fileContent .= $this->tab(2) . 'return $this->' . $fieldNames[$index] . ';' . PHP_EOL;
                $fileContent .= $this->tab() . '}' . PHP_EOL;

                // Setter
                $fileContent .= $this->tab() . 'public function set' . ucfirst($propertyName) . '($' . $propertyName . ') {' . PHP_EOL;
                    $fileContent .= $this->tab(2) . '$this->' . $fieldNames[$index] . ' = $' . $propertyName . ';' . PHP_EOL;
                $fileContent .= $this->tab() . '}' . PHP_EOL;

            }

        $fileContent .= PHP_EOL;
        $fileContent .= '}';

        /* ######################### CLASS END ############################# */

        // Write the file content
        if(!fputs($fileOpen,'<?php' . PHP_EOL . $fileContent)) {
            $this->history[] = '<span style="color:red;">Erreur dans la création du ficher pour la classe ' . $classNameWithoutNs . '</span><br/><br/>';
        } else {
            $this->history[] = '<span style="color:green;">Création du ficher réussie pour la classe ' . $classNameWithoutNs . '</span><br/><br/>';
        }
        fclose($fileOpen);

    }

    /**
     * @param string $table
     * @param null|string $schema
     *
     * @return string
     */
    protected function getStructure($table, $schema = null) {

        try {

            $schema = isset($schema) ? $schema : rf_config('database.name');
            $query = new Query('describe', $table, $schema);
            $fields = $query->toArrayAssoc(true);

            if(isset($fields) && count($fields) > 0) {

                $structure = '[';

                foreach($fields as $field) {

                    $field['Key'] = $field['Key'] == 'MUL' && Architect::is_fk($field['Field'], $table) ? 'FK' : $field['Key'];

                    if(Entity::isDbStructureException($table, $field['Field'])) {

                        foreach(Entity::getDbStructureException($table, $field['Field']) as $fieldException => $valueException) {
                            $field[$fieldException] = $valueException;
                        }

                    }

                    $structure .= PHP_EOL;
                    $structure .= $this->tab(2). '\'' . $field['Field'] . '\'=> [' . PHP_EOL .
		                            $this->tab(3) . '\'Type\'    => \'' . str_replace('\'', '\\\'', $field['Type']) . '\',' . PHP_EOL .
		                            $this->tab(3) . '\'Null\'    => \'' . $field['Null'] . '\',' . PHP_EOL .
		                            $this->tab(3) . '\'Key\'     => \'' . $field['Key'] . '\',' . PHP_EOL .
		                            $this->tab(3) . '\'Default\' => \'' . $field['Default'] . '\',' . PHP_EOL .
		                            $this->tab(3) . '\'Extra\'   => \'' . $field['Extra'] . '\',' . PHP_EOL .
		                            $this->tab(2) . '],';

                }

                $structure = substr($structure, 0, strlen($structure)-1);
                $structure .=  PHP_EOL . $this->tab(1) . ']';
                $this->history[] =  '<span style="color:green;">Le schéma de la table "'.$table.'" a été défini avec succès.</span><br/>';

                return $structure;

            } else {
                throw new DebugException(Log::TYPE_ERROR, 'Impossible de récupérer le schéma');
            }

        } catch(DebugException $e) {

            $this->history[] =  '<span style="color:red;">Le schéma de la table "'.$table.'" n\'a pas pu être défini.</span><br/>';

	        return '';

        }

    }

    /**
     * Remove unused files in the entities directory
     *
     * @param array $files
     */
    protected function cleanEntitiesDirectory($connName, $files) {

        $directoryOpen = opendir(rf_dir('entities') . '/models/c_' . $connName);

        while(($file = readdir($directoryOpen)) !== false) {
            if($file != "." && $file != ".." && !in_array($file, $files)) {
                unlink(rf_dir('entities') . '/models/c_' . $connName . '/' . $file);
            }
        }

        closedir($directoryOpen);

        if(count(glob(rf_dir('entities') . '/models/c_' . $connName . '/' . '*.php')) == count($files)) {
            $this->history[] =  '<span style="color:green;">Les fichiers supplémentaires ont été supprimés avec succès.</span><br/><br/>';
        } else {
            $this->history[] =  '<span style="color:red;">Les fichiers supplémentaires n\'ont pas pu être supprimés en totalité (' . count(glob(rf_dir('entities') . '/model/' . '*.php')) . '/' . (count($files)) . ').</span><br/><br/>';
        }

    }
    
    /**
     * Cette fonction permet de tester si le champ est une clé étrangère ou non.
     *
     * @param string $field
     * @param string $table
     * @param string $database
     *
     * @return bool
     */
    public function is_fk($field, $table = NULL, $database = NULL) {
        $database = $database != NULL ? $database : rf_config('database.name');
        // On test le mode de clé étrangère
        if(rf_config('options.fk-mode') == 'sql') {
            // Si le traitement doit se faire par la base de donnée on test si la vue nécessaire existe
//            if(DatabaseTools::tableExist('view_fk', $database) === true) {
//                // Si oui on prépare la requète
//                $query = new Query('select', 'view_fk', $database);
//                $query->addWhereClauseEqual('TABLE_SCHEMA', $database);
//                $query->addWhereClauseAndEqual('TABLE_NAME', $table);
//                $query->addWhereClauseAndEqual('COLUMN_NAME', $field);
//                $query->addWhereClauseAndEqual('REFERENCED_TABLE_SCHEMA', $database);
//                $query->addWhereClauseAndEqual('REFERENCED_TABLE_NAME', Name::fkToTable($field));
//                $query->addWhereClauseAndEqual('REFERENCED_COLUMN_NAME', 'id');
//                // On récupère la ligne correspondante à la FK si elle existe
//                return count($query->toArrayAssoc(true)) == 1 ? true : false;
//            } else {
//                try {
//                    // Si la vue n'existe pas on crée la vue
//                    $query = 'CREATE OR REPLACE VIEW `'.$database.'`.`view_fk` AS
//                                SELECT CONSTRAINT_NAME, TABLE_SCHEMA, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_SCHEMA, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
//                                FROM `information_schema`.`KEY_COLUMN_USAGE`
//                                WHERE CONSTRAINT_NAME LIKE "fk_%";';
//                    if($query->execute()) {
//                        // Si la création a marché on relance la fonction
//                        $this->is_fk($field, $table, $database);
//                    } else {
//                        throw new BaseException(get_called_class(), 'La table des clés étrangères n\'a pas pu être créée.');
//                    }
//                } catch(BaseException $e) {
//                    $this->history[] =  '<span style="color:red;">La table des clés étrangères n\'a pas pu être créée.</span><br/><br/>';
//                }
//            }
        } elseif(rf_config('options.fk-mode') == 'class') {
            return substr($field, -3, 3) == '_id' ? true : false;
        }
    }

}