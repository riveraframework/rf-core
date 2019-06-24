<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Service\Interfaces;

/**
 * Interface ServiceLauncherInterface
 *
 * @package Rf\Core\Service\Interfaces
 */
interface ServiceLauncherInterface {

    /**
     * ServiceLauncher constructor.
     *
     * @param callable $launcher
     */
    public function __construct(callable $launcher);

    /**
     * Launch the service
     *
     * @return ServiceInterface
     */
    public function launch();

}