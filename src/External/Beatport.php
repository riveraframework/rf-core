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

/**
 * Class Beatport
 *
 * @since 1.0
 *
 * @package Rf\Core\External
 */
class Beatport {


    public $req_url = 'https://oauth-api.beatport.com/identity/1/oauth/request-token';
    public $authurl = 'https://oauth-api.beatport.com/identity/1/oauth/authorize';
    public $auth_submiturl = "https://oauth-api.beatport.com/identity/1/oauth/authorize-submit";
    public $acc_url = 'https://oauth-api.beatport.com/identity/1/oauth/access-token';

    /**
     * Init Facebook class with the params specified in configuration file
     *
     * @return void
     */
    public function init() {

        // Beatport URLs. Note the oauth_callback after the request url. This is needed to catch the verifier string:


        $conskey = ''; // Beatport Consumer Key
        $conssec = ''; // Beatport Consumer Secret
        $beatport_login = "bpusername"; // Beatport Username
        $beatport_password = "bppassword"; // Beatport Password

        /**
         * Step 1: Get a Request token
         */
        $oauth = new OAuth($conskey,$conssec);
        $oauth->enableDebug();
        $oauth->setAuthType(OAUTH_AUTH_TYPE_FORM); // switch to POST request
        $request_token_info = $oauth->getRequestToken($req_url);

        if(!empty($request_token_info)) {
            echo 'Received OAuth Request token: ' . $request_token_info['oauth_token']."\n";
            echo 'Received OAuth Request token secret: ' . $request_token_info['oauth_token_secret']."\n";
        } else {
            print "Failed fetching request token, response was: " . $oauth->getLastResponse();
            exit();
        }

        /**
         * Step 2: Set Request Token to log in
         */
        $oauth->setToken($request_token_info['oauth_token'],$request_token_info['oauth_token_secret']);

        /**
         * Step 3: Use request token to log in and authenticate for 3-legged auth. The response (via callback URL in $req_url) contains the OAuth token and verifier
         */
        ini_set('max_execution_time', 500);
        $submit = "Login";
        $url = $auth_submiturl;

        $curl_connection_bp = curl_init();
        curl_setopt($curl_connection_bp, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_connection_bp, CURLOPT_URL, $url);
        curl_setopt($curl_connection_bp, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($curl_connection_bp, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT6.0; en-US; rv:1.8.1.11) Gecko/20071127 Firefox/2.0.0.11");
        curl_setopt($curl_connection_bp, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($curl_connection_bp, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_connection_bp, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl_connection_bp, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl_connection_bp, CURLOPT_VERBOSE, false); // when true, this outputs the oauth_token and oauth_verifier value that are posted to the callback URL
        curl_setopt($curl_connection_bp, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
        curl_setopt($curl_connection_bp, CURLOPT_REFERER, $curl_connection_bp);
        curl_setopt($curl_connection_bp, CURLOPT_FAILONERROR, 0);
        $post_string = 'oauth_token='.$request_token_info['oauth_token'] . '&username=' . $beatport_login . '&password=' . $beatport_password . '&submit=Login';
        curl_setopt($curl_connection_bp, CURLOPT_POST, true);
        curl_setopt($curl_connection_bp, CURLOPT_POSTFIELDS, $post_string);
        $beatport_response = curl_exec($curl_connection_bp);
        $beatport_response = json_decode($beatport_response);

        print_r($beatport_response);

        /**
         * Step 4: Use verifier string to request the Access Token
         */
        $get_access_token = $oauth->getAccessToken($acc_url, "", $beatport_response->oauth_verifier);
        if(!empty($get_access_token)) {
            print_r($get_access_token);
        } else {
            print "Failed fetching access token, response was: " . $oauth->getLastResponse();
            exit();
        }

        /**
         * Step 5: Set Access Token for further requests
         */
        $oauth->setToken($get_access_token['oauth_token'],$get_access_token['oauth_token_secret']);

        /**
         * Step 6: Test request.
         */
        $oauth->fetch('https://oauth-api.beatport.com/catalog/3/artists/detail', array('id'=>'45250'));
        $json = json_decode($oauth->getLastResponse());

        print_r($json);

    }

}