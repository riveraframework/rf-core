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

use Rf\Core\Exception\SilentException;

/**
 * Class GoogleMaps
 *
 * @since 1.0
 *
 * @package Rf\Core\External
 */
class GoogleMaps {

    /**
     * Get a Google location from an address using the Google Maps API
     *
     * @since 1.0
     *
     * @param string $address
     * @param null|string $language
     * @return bool|mixed
     */
    public function getLocationFromAddress($address, $language = null) {
        
        try {

            // Prepare the request URI
            $formatted_address = urlencode(str_replace(' ', '+', $address));
            $curl_url = 'http://maps.google.com/maps/api/geocode/json?address=' . $formatted_address;
            if(isset($language)) {
                $curl_url .= '&language=' . $language;
            }

            // Start CURL
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

            if (!isset($response) || $response->status !== 'OK') {
                throw new SilentException(get_called_class(), 'Unable to retrieve informations from Google Maps API for given address');
            } else {
                return $response;
            }

        } catch(SilentException $e) {
            return false;
        }
    }
}