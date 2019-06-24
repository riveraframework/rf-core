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
 * Class FileClassGenerator
 *
 * @package Rf\Core\Orm\FileGenerators
 */
class FileClassGenerator extends FileGenerator {

    /**
     * FileModelGenerator constructor.
     *
     * @param string $connName
     * @param string $tableName
     */
    public function __construct($connName, $tableName) {

        $this->connName = $connName;
        $this->tableName = $tableName;

    }

    /**
     * Get the generate file name
     *
     * @return string
     */
    public function getFilename() {

        if(!isset($this->filename)) {

            $className = Name::tableToClass($this->tableName);

            $this->fileName = $className;

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

            $this->filePath = rf_dir('entities') . '/classes/c_' . $this->connName . '/' . $fileName;

        }

        return $this->filePath;

    }

    /**
     * Generate the class file content
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
        $file->append($this->getFilePartClassDeclarationEnd());

        // Write the file only if one doesn't already exist
        if(!file_exists($this->getFilePath())) {
            $file->setFilePath($this->getFilePath());
            $file->write();
        }

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
        $ns .= 'namespace App\\Entities\\Classes\\C_' . $this->connName . ';' . PHP_EOL;
        $ns .= PHP_EOL;

        return $ns;

    }

    /**
     * Generate the imports section
     *
     * @return string
     */
    protected function getFilePartImports() {

        $modelName = Name::tableToClass($this->tableName) . 'Model';

        $imports = '';
        $imports .= 'use App\\Entities\\Classes\\C_;' . $this->connName . '\\' . $modelName . PHP_EOL;
        $imports .= PHP_EOL;

        return $imports;

    }

    /**
     * Generate the class declaration
     *
     * @return string
     */
    protected function getFilePartClassDeclarationStart() {

        $className = Name::tableToClass($this->tableName);
        $modelName = $className . 'Model';

        $classStart = '';
        $classStart .= 'abstract class ' . $className . ' extends ' . $modelName . ' {' . PHP_EOL;
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

}