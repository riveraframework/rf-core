<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Api;

use App\Entities\Interfaces\OwnerInterface;
use App\Entities\OAuth2AccessToken;
use App\Entities\OAuth2Client;
use App\Entities\OAuth2GrantType;
use League\OAuth2\Server\Exception\OAuthServerException;
use Rf\Core\Authentication\Exceptions\AuthenticationException;
use Rf\Core\Authentication\OAuth2\OAuth2ResourceSever;
use Rf\Core\Authentication\OAuth2\Request\Psr7Request;
use Rf\Core\Http\JsonResponse;
use Rf\Core\Http\Response;
use Rf\Core\I18n\I18n;

/**
 * Class ApiController
 *
 * @package Rf\Core\Api
 */
abstract class ApiController {

    /** @var Psr7Request $request */
    private $psr7Request;

    /** @var OAuth2AccessToken $accessToken */
    private $accessToken;

    /**
     * Init controller
     */
    public function __construct() {

        // Get request params
        $this->psr7Request = Psr7Request::create(rf_request());

	    // Force language if the parameter is set and valid
	    $language = rf_request()->getGetData()->get('language');
	    if (!empty($language)) {
		    I18n::setCurrentLanguage($language);
	    }

    }

    /**
     *
     * @throws AuthenticationException
     */
    protected function validateAuthorization() {

        $oauth2Server = new OAuth2ResourceSever();
        $oauth2Server->setPublicKey(rf_config('oauth2.public-key'));
        $service = $oauth2Server->getService();

        try {

            $service->validateAuthenticatedRequest($this->getPsr7Request());

        } catch (OAuthServerException $e) {

            $response = [
                'status' => 'error',
                'error' => $e->getMessage(),
                'message' => $e->getHint(),
                'trace' => $e->getTraceAsString(),
            ];
            $response = new JsonResponse(401, $response);
            $response->send();

        } catch (\Exception $e) {

            throw new AuthenticationException('Authentication error', null, $e);

        }

    }

    /**
     * Get current token
     *
     * @TODO: Bearer case
     * @TODO: [LATER] https://developers.facebook.com/docs/graph-api/securing-requests
     *
     * @return OAuth2AccessToken
     * @throws AuthenticationException
     */
    protected function getToken() {

        if(empty($this->accessToken)) {

            // Get access token from url
            $accessToken = rf_request()->getGetData()->get('access_token');

            if(!empty($accessToken)) {

                /** @var OAuth2AccessToken $token */
                $token = OAuth2AccessToken::findFirstBy('access_token = "' . $accessToken . '"');

                if($token) {

                    $this->accessToken = $token;

                    return $token;

                }

            }

        } else {
            return $this->accessToken;
        }

        throw new AuthenticationException('Unable to get the client linked to this access token');

    }

    /**
     * Get current client
     *
     * @return OAuth2Client
     * @throws AuthenticationException
     */
    protected function getClient() {

        $this->getToken();

        if(!empty($this->accessToken)) {
            return $this->accessToken->getSession()->getClient();
        }

        throw new AuthenticationException('Unable to get the client linked to this access token');

    }

    /**
     * Get owner
     *
     * @return null
     */
    public abstract function getOwner();

    /**
     * Get current request (psr7)
     *
     * @return Psr7Request
     */
    protected function getPsr7Request() {

        return $this->psr7Request;

    }

    /**
     * Send the response to the client
     *
     * @param int $httpCode
     * @param array $data
     *
     * @return Response
     */
    protected function getResponse($httpCode, array $data) {

        $format = rf_request()->getGetData()->get('format');

        // @TODO: Move this part to the $app->after() method?
        switch ($format) {

//            case 'xml':
//                return new XmlResponse($httpCode, $data);
//                break;

            case 'json':
            default:
                return new JsonResponse($httpCode, $data);
                break;

        }

    }

}