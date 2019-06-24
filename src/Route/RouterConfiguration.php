<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Route;

use Rf\Core\Config\ConfigurationSet;

/**
 * Class RouterConfiguration
 *
 * @package Rf\Core\Route
 */
class RouterConfiguration extends ConfigurationSet {

    /**
     * Get the base url for routing/linking
     *
     * @return string
     */
    public function getBaseUrl() {

        return $this->get('url');

    }

}
