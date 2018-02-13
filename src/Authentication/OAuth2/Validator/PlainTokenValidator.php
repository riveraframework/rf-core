<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Authentication\OAuth2\Validator;

use App\Entities\OAuth2AccessToken;
use League\OAuth2\Server\AuthorizationValidators\AuthorizationValidatorInterface;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\CryptTrait;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class PlainTokenValidator
 *
 * @package App\OAuth2\Classes
 */
class PlainTokenValidator implements AuthorizationValidatorInterface
{
    use CryptTrait;

    /**
     * @var AccessTokenRepositoryInterface
     */
    private $accessTokenRepository;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var CryptKey
     */
    private $publicKey;

    /**
     * @param AccessTokenRepositoryInterface $accessTokenRepository
     */
    public function __construct(AccessTokenRepositoryInterface $accessTokenRepository, $accessToken)
    {
        $this->accessTokenRepository = $accessTokenRepository;
        $this->accessToken = $accessToken;
    }

    /**
     * Set the public key
     *
     * @param \League\OAuth2\Server\CryptKey $key
     */
    public function setPublicKey(CryptKey $key)
    {
        $this->publicKey = $key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthorization(ServerRequestInterface $request)
    {

        // Check if token has been revoked
        if ($this->accessTokenRepository->isAccessTokenRevoked($this->accessToken)) {
            throw OAuthServerException::accessDenied('Access token has been revoked');
        }

        /** @var OAuth2AccessToken $oauth2AccessToken */
        $oauth2AccessToken = OAuth2AccessToken::findFirstBy('access_token = "' . $this->accessToken . '"');
        $nowDateTime = new \DateTime();

        if($nowDateTime > $oauth2AccessToken->getExpiryDateTime()) {
            throw OAuthServerException::accessDenied('Access token is expired');
        }

        // Get session from token
        $session = $oauth2AccessToken->getSession();

        if(!$session) {
            throw OAuthServerException::accessDenied('Unable to retrieve the session');
        }

        // Get token scopes
        $scopes = [];
        foreach ($oauth2AccessToken->getScopes() as $scope) {
            $scopes[] = $scope->getScope();
        }

        // Return the request with additional attributes
        return $request
            ->withAttribute('oauth_access_token_id', $oauth2AccessToken->getIdentifier())
            ->withAttribute('oauth_client_id', $session->getOauth2ClientId())
            ->withAttribute('oauth_user_id', $session->getOauth2UserId())
            ->withAttribute('oauth_scopes', implode(' ', $scopes));

    }

}
