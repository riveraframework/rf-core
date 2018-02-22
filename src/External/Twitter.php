<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\External;

require_once 'OAuth.php';

/**
 * Class Twitter
 *
 * @since 1.0
 *
 * @package Rf\Core\External
 */
class Twitter extends OAuthAdapter {

    /**
     * @var string
     */
    public $host = 'https://api.twitter.com/1.1/';

}