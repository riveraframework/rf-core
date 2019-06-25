<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Cache;

use Rf\Core\Config\ConfigurationSet;

/**
 * Class CacheConfiguration
 *
 * @package Rf\Core\Cache
 */
class CacheConfiguration extends ConfigurationSet {

    /**
     * Get the handlers
     *
     * @return CacheConfiguration
     */
    public function getHandlers() {

        return $this->get('handlers');

    }

}
