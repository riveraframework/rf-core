<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Authentication\OAuth2;

use App\Entities\Repositories\OAuth2AccessTokenRepository;
use League\OAuth2\Server\ResourceServer;
use Rf\Core\Authentication\OAuth2\Validator\PlainTokenValidator;

/**
 * Class OAuth2ResourceSever
 *
 * @package Rf\Core\Authentication
 */
class OAuth2ResourceSever {

    /** @var string Path to the public key file */
    protected $publicKeyPath;

    /**
     * Use a custom public key
     *
     * @param $publicKeyPath
     */
    public function setPublicKey($publicKeyPath) {

        $this->publicKeyPath = $publicKeyPath;

    }

    /**
     * Get the resource service
     *
     * @return ResourceServer
     * @throws \Exception
     */
    public function getService() {

        // Init our repositories
        $accessTokenRepository = new OAuth2AccessTokenRepository(); // instance of AccessTokenRepositoryInterface

        $publicKey = rf_dir('config') . '/security/oauth2-public.cert';
        if(!empty($this->publicKeyPath)) {
            $publicKey = $this->publicKeyPath;
        }

        // Get access token from url
        $accessToken = rf_request()->getGetData()->get('access_token');

        // Init validator
        if(!empty($accessToken)) {
            $authorizationValidator = new PlainTokenValidator($accessTokenRepository, $accessToken);
        } else {
            $authorizationValidator = null;
        }

        // Setup the resource server
        $server = new ResourceServer(
            $accessTokenRepository,
            $publicKey,
            $authorizationValidator
        );

        return $server;

    }

}