<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Session;

use Rf\Core\Config\ConfigurationSet;

/**
 * Class SessionConfiguration
 *
 * @package Rf\Core\Session
 */
class SessionConfiguration extends ConfigurationSet {

    /**
     * Get the handlers
     *
     * @return SessionConfiguration
     */
    public function getHandlers() {

        return $this->get('handlers');

    }

}
