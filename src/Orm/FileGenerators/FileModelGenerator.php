<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Orm\FileGenerators;

use Rf\Core\System\FileSystem\File;
use Rf\Core\Utils\Format\Name;

/**
 * Class FileModelGenerator
 *
 * @package Rf\Core\Orm\FileGenerators
 */
class FileModelGenerator extends FileGenerator {

    /** @var array */
    protected $fields;

    /**
     * FileModelGenerator constructor.
     *
     * @param string $connName
     * @param string $tableName
     * @param array $fields
     */
    public function __construct($connName, $tableName, array $fields) {

        $this->connName = $connName;
        $this->tableName = $tableName;
        $this->fields = $fields;

    }

    /**
     * Get the generate file name
     *
     * @return string
     */
    public function getFilename() {

        if(!isset($this->filename)) {

            $className = Name::tableToClass($this->tableName);

            $this->fileName = $className . 'Model.php';

        }

        return $this->filename;

    }

    /**
     * Get the file path
     *
     * @return string
     */
    public function getFilePath() {

        if(!isset($this->filePath)) {

            $fileName = $this->getFilename();

            $this->filePath = rf_dir('entities') . '/models/c_' . $this->connName . '/' . $fileName;

        }

        return $this->filePath;

    }

    /**
     * Generate the model file content
     *
     * @TODO: Add phpDoc to generated files
     *
     * @return File
     * @throws \Exception
     */
    public function generate() {

        // Generate the file content
        $file = new File();
        $file->append($this->getFilePartHeader());
        $file->append($this->getFilePartNamespace());
        $file->append($this->getFilePartImports());
        $file->append($this->getFilePartClassDeclarationStart());
        $file->append($this->getFilePartConstants());
        $file->append($this->getFilePartProperties());
        $file->append($this->getFilePartGettersSetters());
        $file->append($this->getFilePartClassDeclarationEnd());

        // Write the file
        $file->setFilePath($this->getFilePath());
        $file->write();

        return $file;

    }

    /**
     * Generate the header section
     *
     * @return string
     */
    protected function getFilePartHeader() {

        $header = '';
        $header .= '<?php' . PHP_EOL;
        $header .= '/* ########################################################################## *' . PHP_EOL;
        $header .= ' * #####################   Auto Generated File    ########################### *' . PHP_EOL;
        $header .= ' * #####################      DO NOT MODIFY       ########################### *' . PHP_EOL;
        $header .= ' * ########################################################################## */' . PHP_EOL;
        $header .= PHP_EOL;

        return $header;

    }

    /**
     * Generate the namespace section
     *
     * @return string
     */
    protected function getFilePartNamespace() {

        $ns = '';
        $ns .= 'namespace App\\Entities\\Models\\C_' . $this->connName . ';' . PHP_EOL;
        $ns .= PHP_EOL;

        return $ns;

    }

    /**
     * Generate the imports section
     *
     * @return string
     */
    protected function getFilePartImports() {

        $imports = '';
        $imports .= 'use Rf\Core\\Orm\\Entity;' . PHP_EOL;
        $imports .= PHP_EOL;

        return $imports;

    }

    /**
     * Generate the class declaration
     *
     * @return string
     */
    protected function getFilePartClassDeclarationStart() {

        $classStart = '';
        $classStart .= 'abstract class ' . Name::tableToClass($this->tableName) . 'Model extends Entity {' . PHP_EOL;
        $classStart .= PHP_EOL;

        return $classStart;

    }

    /**
     * Generate the class end
     *
     * @return string
     */
    protected function getFilePartClassDeclarationEnd() {

        $classEnd = '';
        $classEnd .= '}';
        $classEnd .= PHP_EOL;

        return $classEnd;

    }

    /**
     * Generate the entity constants
     *
     * @return string
     */
    protected function getFilePartConstants() {

        $structure = '[';

        foreach($this->fields as $field) {

            $structure .= PHP_EOL;
            $structure .= $this->tab(2). '\'' . $field['Field'] . '\'=> [' . PHP_EOL .
                $this->tab(3) . '\'Type\'    => \'' . str_replace('\'', '\\\'', $field['Type']) . '\',' . PHP_EOL .
                $this->tab(3) . '\'Null\'    => \'' . $field['Null'] . '\',' . PHP_EOL .
                $this->tab(3) . '\'Key\'     => \'' . $field['Key'] . '\',' . PHP_EOL .
                $this->tab(3) . '\'Default\' => \'' . $field['Default'] . '\',' . PHP_EOL .
                $this->tab(3) . '\'Extra\'   => \'' . $field['Extra'] . '\',' . PHP_EOL .
                $this->tab(3) . '\'Fk\'   => \'' . $field['Fk'] . '\',' . PHP_EOL .
                $this->tab(3) . '\'Property\'   => \'' . $field['Property'] . '\',' . PHP_EOL .
                $this->tab(2) . '],';

        }

        $structure .=  PHP_EOL . $this->tab(1) . ']';

        $constants = '';
        $constants .= $this->tab() . '// Constants' . PHP_EOL;
        $constants .= $this->tab() . 'const conn_name = \'' . $this->connName . '\';' . PHP_EOL;
        $constants .= $this->tab() . 'const table_name = \'' . $this->tableName . '\';' . PHP_EOL;
        $constants .= $this->tab() . 'const table_structure = ' . $structure . ';' . PHP_EOL;
        $constants .= PHP_EOL;

        return $constants;

    }

    /**
     * Generate the entity properties
     *
     * @return string
     */
    protected function getFilePartProperties() {

        $properties = '';
        $properties .= $this->tab() . '// Properties' . PHP_EOL;
        foreach($this->fields as $field) {
            $properties .= $this->tab() . 'protected $' . $field['Property'] . ';' .PHP_EOL;
        }
        $properties .= PHP_EOL;

        return $properties;

    }

    /**
     * Generate the entity getters and setters
     *
     * @return string
     */
    protected function getFilePartGettersSetters() {

        $gettersSetters = '';
        $gettersSetters .= $this->tab() . '// Getters and setters' . PHP_EOL;

        foreach ($this->fields as $field) {

            // Getters
            $gettersSetters .= $this->tab() . 'public function get' . ucfirst($field['Property']) . '() {' . PHP_EOL;
            $gettersSetters .= $this->tab(2) . 'return $this->' . $field['Property'] . ';' . PHP_EOL;
            $gettersSetters .= $this->tab() . '}' . PHP_EOL;

            // Setter
            $gettersSetters .= $this->tab() . 'public function set' . ucfirst($field['Property']) . '($' . $field['Property'] . ') {' . PHP_EOL;
            $gettersSetters .= $this->tab(2) . '$this->' . $field['Property'] . ' = $' . $field['Property'] . ';' . PHP_EOL;
            $gettersSetters .= $this->tab() . '}' . PHP_EOL;

        }

        $gettersSetters .= PHP_EOL;

        return $gettersSetters;

    }

}