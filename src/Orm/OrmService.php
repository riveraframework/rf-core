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
use Rf\Core\Log\LogService;
use Rf\Core\Orm\FileGenerators\FileClassGenerator;
use Rf\Core\Orm\FileGenerators\FileModelGenerator;
use Rf\Core\Service\Service;
use Rf\Core\System\FileSystem\DirectoryFactory;

/**
 * Class Architect
 *
 * @TODO: Add base classes to the entities/{conn}/classes folder if they don't exist
 *
 * @package Rf\Core\Orm
 */
class OrmService extends Service {

    /** @var string  */
    const TYPE = 'orm';

    /** @var array Available table list */
    protected $currentTableList = [];

    /** @var array Model file list */
    protected $currentModelFileList = [];

    /** @var array Class file list */
    protected $currentClassFileList = [];
    
    /**
     * Update all entity files using the tables
     *
     * @param string $dbServiceName
     *
     * @throws \Exception
     */
    public function refresh($dbServiceName) {

        $connection = rf_sp()->getDatabase($dbServiceName)->getConnection();

        // Create the destination folder if necessary
        DirectoryFactory::create(rf_dir('entities') . '/models/c_' . $dbServiceName, 0755, true);

        // Get table list
        $this->currentTableList = $connection->getTableList();

        if(isset($this->currentTableList) && count($this->currentTableList) > 0) {

        } elseif(isset($this->currentTableList) && count($this->currentTableList) == 0) {

        } else {

            throw new DebugException(LogService::TYPE_ERROR, 'Unable to retrieve the database structure.');

        }

        // Update the entity files for each table
        foreach($this->currentTableList as $table) {

            // Get the table structure
            $fields = $connection->getTableStructure($table);

            // Generate the model file
            $modelFileGenerator = new FileModelGenerator($dbServiceName, $table, $fields);
            $modelFileGenerator->generate();

            // Generate the class file if missing
            $classFileGenerator = new FileClassGenerator($dbServiceName, $table);
            $classFileGenerator->generate();

            // Add generated file to the list for folder cleanup
            $this->currentModelFileList[] = $modelFileGenerator->getFilename();
            $this->currentClassFileList[] = $classFileGenerator->getFilename();

        }

        // Delete unused entity files
        array_unique($this->currentModelFileList);
        array_unique($this->currentClassFileList);
        $this->cleanEntitiesDirectories($dbServiceName);

    }

    /**
     * Remove unused files in the entities directory
     *
     * @TODO: For now the cleaning in the classes folder is skipped. Add a config param to force it.
     *
     * @param string $connName
     */
    protected function cleanEntitiesDirectories($connName) {

        $directoryOpen = opendir(rf_dir('entities') . '/models/c_' . $connName);

        while(($file = readdir($directoryOpen)) !== false) {
            if($file != "." && $file != ".." && !in_array($file, $this->currentModelFileList)) {
                unlink(rf_dir('entities') . '/models/c_' . $connName . '/' . $file);
            }
        }

        closedir($directoryOpen);

        // @TODO: Check this
        if(count(glob(rf_dir('entities') . '/models/c_' . $connName . '/' . '*.php')) !== count($this->currentModelFileList)) {
            new DebugException(LogService::TYPE_ERROR, 'Unable to clean the entities folders properly (' . count(glob(rf_dir('entities') . '/model/' . '*.php')) . '/' . (count($this->currentModelFileList)) . ')');
        }

    }

}