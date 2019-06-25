<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Debug;

use Rf\Core\Service\Service;

/**
 * Class DebugService
 *
 * @package Rf\Core\Debug
 */
class DebugService extends Service {

    /** @var string  */
    const TYPE = 'debug';

    /** @var DebugConfiguration */
    protected $configuration;

    /**
     * {@inheritDoc}
     *
     * @param array $configuration
     */
    public function loadConfiguration(array $configuration) {

        $this->configuration = new DebugConfiguration($configuration);

    }

    /**
     * {@inheritDoc}
     *
     * @return DebugConfiguration
     */
    public function getConfiguration() {

        return $this->configuration;

    }

}