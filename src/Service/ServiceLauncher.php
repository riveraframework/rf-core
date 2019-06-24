<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Service;

/**
 * Class ServiceLauncher
 *
 * @package Rf\Core\Service
 */
class ServiceLauncher {

    /** @var callable */
    protected $launcher;

    /**
     * {@inheritDoc}
     */
    public function __construct(callable $launcher) {

        $this->launcher = $launcher;

    }

    /**
     * {@inheritDoc}
     */
    public function launch() {

        return ($this->launcher)();

    }

}