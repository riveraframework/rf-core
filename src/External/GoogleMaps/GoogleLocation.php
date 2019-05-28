<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\GoogleMaps;

/**
 * Class GoogleLocation
 *
 * @package Rf\Core\Geography
 */
class GoogleLocation extends Location {

    /**
     * Create a new Location using Google API
     *
     * @param string $address
     * @param null|string $language
     */
    public function __construct($address, $language = null) {
        
        if(isset($language)) {
            $this->setLanguage($language);
        }
        
        if(is_object($address)) {
            $this->parseFromGoogleMapAPI($address);
        } else {
            $this->retrieveFromGoogleMapAPI($address);
        }

    }

}