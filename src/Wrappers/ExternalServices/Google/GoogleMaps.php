<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Wrappers\ExternalServices\Google;

use Rf\Core\Base\Exceptions\SilentException;

/**
 * Class GoogleMaps
 *
 * @TODO: Refactor module
 *
 * *************************************************************************************
 * **** /!\ The code in this module is experimental/incomplete use with caution /!\ ****
 * *************************************************************************************
 *
 * @package Rf\Core\Wrappers\ExternalServices\Google
 */
class GoogleMaps {

    /** @var string Google Maps API key */
    protected $apiKey;

    /**
     * GoogleMaps constructor.
     *
     * @param string $apiKey
     */
    public function __construct($apiKey) {

        $this->apiKey = $apiKey;

    }

    /**
     * Get a Google location from an address using the Google Maps API
     *
     * @param string $address
     * @param null|string $language
     *
     * @return bool|mixed
     */
    public function getLocationFromAddress($address, $language = null) {

        try {

            // Prepare the request URI
            $formatted_address = urlencode(str_replace(' ', '+', $address));
            $curl_url = 'https://maps.google.com/maps/api/geocode/json?address=' . $formatted_address;
            if(isset($this->apiKey)) {
                $curl_url .= '&key=' . $this->apiKey;
            }
            if(isset($language)) {
                $curl_url .= '&language=' . $language;
            }

            // Start CURL
            // @TODO: Use internal Curl
            $curl_session = curl_init();
            curl_setopt($curl_session, CURLOPT_URL, $curl_url);
            curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl_session, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
            $file_contents = curl_exec($curl_session);
            curl_close($curl_session);

            // Process response
            $json = $file_contents;
            $response = json_decode($json);

            if (!isset($response) || $response === false) {
                throw new SilentException(get_called_class(), 'Unable to retrieve information from Google Maps API for given address:' . json_last_error_msg());
            } elseif ($response->status !== 'OK') {
                throw new SilentException(get_called_class(), 'Unable to retrieve information from Google Maps API for given address');
            } else {
                return $response;
            }

        } catch(SilentException $e) {
            return false;
        }

    }

}