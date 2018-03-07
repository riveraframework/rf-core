<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Application;

use Rf\Core\Api\Api;
use Rf\Core\Authentication\Authentication;
use Rf\Core\Autoload;
use Rf\Core\Base\ErrorHandler;
use Rf\Core\Base\GlobalSingleton;
use Rf\Core\Cache\CacheService;
use Rf\Core\Cache\CacheHelpers;
use Rf\Core\Entity\Architect;
use Rf\Core\Exception\BaseException;
use Rf\Core\Exception\ErrorMessageException;
use Rf\Core\Http\Request;
use Rf\Core\I18n\I18n;
use Rf\Core\Routing\Router;
use Rf\Core\Uri\Uri;

/**
 * Class Application
 *
 * @package Rf\Core\Application
 */
class Application extends GlobalSingleton {
    
}