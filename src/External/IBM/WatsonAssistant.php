<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\External\IBM;

use Rf\Core\Http\Curl;
use Rf\Core\Http\Exceptions\CurlException;

/**
 * Class WatsonAssistant
 *
 * @package Rf\Core\External\IBM
 */
class WatsonAssistant {

    /** Watson API url */
    const API_URL = 'https://gateway.watsonplatform.net/assistant/api/v1/';

    /** Supported endpoint list */
    const ENDPOINTS = [
        'message-send' => '/workspaces/[WORKSPACE_ID]/message/',
        'entities-update' => '/workspaces/[WORKSPACE_ID]/entities/[ENTITY]',
    ];

    /** @var string $username */
    protected $username;

    /** @var string $password */
    protected $password;

    /** @var string $workspaceId */
    protected $workspaceId;

    /** @var string $version */
    protected $version = '2018-02-16';

    /**
     * WatsonAssistant constructor.
     *
     * @param string $username
     * @param string $password
     * @param string $workspaceId
     * @param string $version
     */
    public function __construct($username, $password, $workspaceId, $version = null) {

        $this->username = $username;
        $this->password = $password;
        $this->workspaceId = $workspaceId;

        if(isset($version)) {
            $this->version = $version;
        }

    }

    /**
     * Generate the final endpoint url
     *
     * @param string $endpoint
     * @param array $queryVars
     *
     * @return string
     * @throws \Exception
     */
    public function getUrl($endpoint, $queryVars = []) {

        if(!in_array($endpoint, array_keys(static::ENDPOINTS))) {
            throw new \Exception('Endpoint not supported');
        }

        $url = static::API_URL . str_replace(
                '[WORKSPACE_ID]',
                $this->workspaceId,
                static::ENDPOINTS[$endpoint]
            ) . '?version=' . $this->version;

        foreach ($queryVars as $name => $value) {
            $url = str_replace('[' . $name . ']', $value, $url);
        }

        return $url;

    }

    /**
     * Perform a POST request against the API
     *
     * @param string $endpoint
     * @param string|array $data
     * @param array $queryVars
     *
     * @return false|string
     * @throws \Exception
     */
    public function post($endpoint, $data, $queryVars = []) {

        return $this->getResponse('post', $endpoint, $data, $queryVars);

    }

    /**
     * Get the response from the API
     *
     * @param string $method
     * @param string $endpoint
     * @param string|array $data
     * @param array $queryVars
     *
     * @return false|string
     * @throws \Exception
     * @throws CurlException
     */
    protected function getResponse($method, $endpoint, $data, $queryVars = []) {

        $request = new Curl($this->getUrl($endpoint, $queryVars));
        $request->setMethod($method);
        $request->addHeader('Content-Type: application/json');
        $request->setOption(CURLOPT_USERPWD, $this->username . ':' . $this->password);
        $request->setOption(CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        if(!empty($data)) {
            $request->setPostData(is_string($data) ? $data : json_encode($data));
        }

        return $request->getResults();

    }

}