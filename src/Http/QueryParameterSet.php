<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Http;

use Rf\Core\Base\ParameterSet;
use Rf\Core\Routing\Router;

/**
 * Class QueryParameterSet
 *
 * @since 1.0
 *
 * @package Rf\Core\Http
 */
class QueryParameterSet extends ParameterSet {

    /**
     * Get the queried module name
     *
     * @since 1.0
     *
     * @return mixed
     */
    public function appModule() {

        return $this->get(Router::DEFAULT_CONTROLLER_PARAM_SYS);

    }

    /**
     * Get the queried view name
     *
     * @since 1.0
     *
     * @return mixed
     */
    public function appView() {

        return $this->get(Router::DEFAULT_VIEW_PARAM_SYS);

    }

    /**
     * Get the queried subview name
     *
     * @since 1.0
     *
     * @return mixed
     */
    public function appSubView() {

        return $this->get(Router::DEFAULT_SUBVIEW_PARAM_SYS);

    }
    
}