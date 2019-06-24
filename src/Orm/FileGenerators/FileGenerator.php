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

/**
 * Class FileGenerator
 *
 * @package Rf\Core\Orm\FileGenerators
 */
abstract class FileGenerator {

    /** @var string */
    protected $connName;

    /** @var string */
    protected $tableName;

    /** @var string */
    protected $fileName;

    /** @var string */
    protected $filePath;

    /** @var string $indentation Indentation character */
    protected $indentation = "\t";

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

}