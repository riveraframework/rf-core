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

/**
 * Class WatsonAssistant
 *
 * @package Rf\Core\External\IBM
 */
class WatsonAssistant {

    const API_URL = 'https://gateway.watsonplatform.net/assistant/api/v1/';
    const ENDPOINTS = [
        'message-post' => '/workspaces/[WORKSPACE_ID]/message/'
    ];

    protected $username;
    protected $password;
    protected $workspaceId;
    protected $version = '2018-02-16';

    public function __construct($username, $password, $workspaceId, $version = null) {

        $this->username = $username;
        $this->password = $password;
        $this->workspaceId = $workspaceId;

        if(isset($version)) {
            $this->version = $version;
        }

    }

    public function getUrl($method, $endpoint) {

        if(!in_array($endpoint . '-' . $method, array_keys(static::ENDPOINTS))) {
            throw new \Exception('Endpoint not supported');
        }

        return static::API_URL . str_replace(
            '[WORKSPACE_ID]',
            $this->workspaceId,
            static::ENDPOINTS[$endpoint . '-' . $method]
        ) . '?version=' . $this->version;

    }

    public function post($endpoint, $data) {

        return $this->getResponse('post', $endpoint, $data);

    }

    protected function getResponse($method, $endpoint, $data) {

        $request = new Curl($this->getUrl($method, $endpoint));
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