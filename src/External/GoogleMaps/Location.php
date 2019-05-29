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

use Rf\Core\External\GoogleMaps;
use Rf\Core\Utils\Format\Name;

/**
 * Class Location
 *
 * @since 1.0
 *
 * @package Rf\Core\Geography
 */
class Location {

    /**
     * @var string $id
     * @since 1.0
     */
    public $id;

    /**
     * @var string $language
     * @since 1.0
     */
    public $language;

    /**
     * @var string $type
     * @since 1.0
     */
    public $type;

    /**
     * @var \Rf\Core\GoogleMaps\BaseCountry $country
     * @since 1.0
     */
    public $country;

    /**
     * @var \Rf\Core\GoogleMaps\BaseAdministrativeArea $administrativeAreaLevel1
     * @since 1.0
     */
    public $administrativeAreaLevel1;

    /**
     * @var \Rf\Core\GoogleMaps\BaseAdministrativeArea $administrativeAreaLevel2
     * @since 1.0
     */
    public $administrativeAreaLevel2;

    /**
     * @var \Rf\Core\GoogleMaps\BaseAdministrativeArea $administrativeAreaLevel3
     * @since 1.0
     */
    public $administrativeAreaLevel3;

    /**
     * @var \Rf\Core\GoogleMaps\BaseAdministrativeArea $administrativeAreaLevel4
     * @since 1.0
     */
    public $administrativeAreaLevel4;

    /**
     * @var \Rf\Core\GoogleMaps\BaseAdministrativeArea $administrativeAreaLevel5
     * @since 1.0
     */
    public $administrativeAreaLevel5;

    /**
     * @var int
     * @since 1.0
     */
    public $administrativeAreaLevelMax = 0;

    /**
     * @var \Rf\Core\GoogleMaps\BasePostalCode $postalCode
     * @since 1.0
     */
    public $postalCode;

    /**
     * @var \Rf\Core\GoogleMaps\BaseLocality $locality
     * @since 1.0
     */
    public $locality;

    /**
     * @var \Rf\Core\GoogleMaps\BaseStreet $street
     * @since 1.0
     */
    public $street;
    
    /**
     * @var string $coordinateType Available :  street_number|street_name|locality|country
     * @since 1.0
     */
    public $coordinateType;

    /**
     * @var
     * @since 1.0
     */
    public $lat;

    /**
     * @var
     * @since 1.0
     */
    public $lng;

    /**
     * @var string $data
     * @since 1.0
     */
    public $data;

    /**
     * @var array $errors
     * @since 1.0
     */
    public $errors = array();

    /**
     * @var array $availableAdministrativeAreas
     * @since 1.0
     */
    public static $availableAdministrativeAreas = array(
        1 => 'administrative_area_level_1', 
        2 => 'administrative_area_level_2', 
        3 => 'administrative_area_level_3', 
        4 => 'administrative_area_level_4', 
        5 => 'administrative_area_level_5'
    );

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return BaseCountry
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param BaseCountry $country
     */
    public function setCountry(BaseCountry $country)
    {
        $this->country = $country;
    }

    /**
     * @return BaseAdministrativeArea
     */
    public function getAdministrativeAreaLevel1()
    {
        return $this->administrativeAreaLevel1;
    }

    /**
     * @param BaseAdministrativeArea $administrativeAreaLevel1
     */
    public function setAdministrativeAreaLevel1(BaseAdministrativeArea $administrativeAreaLevel1)
    {
        $this->administrativeAreaLevel1 = $administrativeAreaLevel1;
    }

    /**
     * @return BaseAdministrativeArea
     */
    public function getAdministrativeAreaLevel2()
    {
        return $this->administrativeAreaLevel2;
    }

    /**
     * @param BaseAdministrativeArea $administrativeAreaLevel2
     */
    public function setAdministrativeAreaLevel2(BaseAdministrativeArea $administrativeAreaLevel2)
    {
        $this->administrativeAreaLevel2 = $administrativeAreaLevel2;
    }

    /**
     * @return BaseAdministrativeArea
     */
    public function getAdministrativeAreaLevel3()
    {
        return $this->administrativeAreaLevel3;
    }

    /**
     * @param BaseAdministrativeArea $administrativeAreaLevel3
     */
    public function setAdministrativeAreaLevel3(BaseAdministrativeArea $administrativeAreaLevel3)
    {
        $this->administrativeAreaLevel3 = $administrativeAreaLevel3;
    }

    /**
     * @return BaseAdministrativeArea
     */
    public function getAdministrativeAreaLevel4()
    {
        return $this->administrativeAreaLevel4;
    }

    /**
     * @param BaseAdministrativeArea $administrativeAreaLevel4
     */
    public function setAdministrativeAreaLevel4(BaseAdministrativeArea $administrativeAreaLevel4)
    {
        $this->administrativeAreaLevel4 = $administrativeAreaLevel4;
    }

    /**
     * @return BaseAdministrativeArea
     */
    public function getAdministrativeAreaLevel5()
    {
        return $this->administrativeAreaLevel5;
    }

    /**
     * @param BaseAdministrativeArea $administrativeAreaLevel5
     */
    public function setAdministrativeAreaLevel5(BaseAdministrativeArea $administrativeAreaLevel5)
    {
        $this->administrativeAreaLevel5 = $administrativeAreaLevel5;
    }

    /**
     * @return int
     */
    public function getAdministrativeAreaLevelMax()
    {
        return $this->administrativeAreaLevelMax;
    }

    /**
     * @param int $administrativeAreaLevelMax
     */
    public function setAdministrativeAreaLevelMax($administrativeAreaLevelMax)
    {
        $this->administrativeAreaLevelMax = $administrativeAreaLevelMax;
    }

    /**
     * @return BasePostalCode
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * @param BasePostalCode $postalCode
     */
    public function setPostalCode(BasePostalCode $postalCode)
    {
        $this->postalCode = $postalCode;
    }

    /**
     * @return BaseLocality
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * @param BaseLocality $locality
     */
    public function setLocality(BaseLocality $locality)
    {
        $this->locality = $locality;
    }

    /**
     * @return BaseStreet
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param BaseStreet $street
     */
    public function setStreet(BaseStreet $street)
    {
        $this->street = $street;
    }

    /**
     * @return mixed
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * @param mixed $lat
     */
    public function setLat($lat)
    {
        $this->lat = $lat;
    }

    /**
     * @return mixed
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * @param mixed $lng
     */
    public function setLng($lng)
    {
        $this->lng = $lng;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param array $errors
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @return array
     */
    public static function getAvailableAdministrativeAreas()
    {
        return self::$availableAdministrativeAreas;
    }

    /**
     * @param array $availableAdministrativeAreas
     */
    public static function setAvailableAdministrativeAreas(array $availableAdministrativeAreas)
    {
        self::$availableAdministrativeAreas = $availableAdministrativeAreas;
    }
        
    /**
     *
     *
     * @param string $address
     * @param string $language don't override the class language param
     * @return boolean|\Rf\Core\GoogleMaps\Location
     * @return void
     *@since 1.0
     *
     */
    public function retrieveFromGoogleMapAPI($address, $language = null) {
        
        if(!isset($language)) {
            $language = $this->language;
        }
        
        $google_maps_API = new GoogleMaps();
        $location_data = $google_maps_API->getLocationFromAddress($address, $language);
        if($location_data) {
            $this->data = json_encode($location_data);
            return $this->parseFromGoogleMapAPI($location_data->results[0]);
        } else {
            return false;
        }
    }

    /**
     *
     * @param $googleApiObject
     *
     * @return $this
     */
    public function parseFromGoogleMapAPI($googleApiObject) {

        if(!empty($googleApiObject->place_id)) {
            $this->id = $googleApiObject->place_id;
        }
        
        foreach($googleApiObject->address_components as $address_component) {

            if($address_component->types[0] === 'street_number') {

                if(!isset($this->street)) {
                    $this->street = new BaseStreet();
                }

                $this->street->number = $address_component->long_name;

            } elseif($address_component->types[0] === 'postal_code') {

                if(!isset($this->postalCode)) {
                    $this->postalCode = new BasePostalCode();
                }

                $this->postalCode->code = $address_component->long_name;

            } elseif($address_component->types[0] === 'route') {

                if(!isset($this->street)) {
                    $this->street = new BaseStreet();
                }

                $this->street->route = $address_component->long_name;

            } elseif($address_component->types[0] === 'locality') {

                if(!isset($this->locality)) {
                    $this->locality = new BaseLocality();
                }

                $this->locality->name = $address_component->long_name;

            } elseif(in_array($address_component->types[0], self::$availableAdministrativeAreas)) {

                $propName = Name::fieldToProperty($address_component->types[0]);

                if(!isset($this->{$address_component->types[0]})) {
                    $this->{$propName} = new BaseAdministrativeArea();
                }

                $this->{$propName}->code = $address_component->short_name;
                $this->{$propName}->name = $address_component->long_name;

                foreach (self::$availableAdministrativeAreas as $level => $ae) {

                    if($address_component->types[0] === $ae) {

                        $this->{$propName}->level = $level;

                        if($level > $this->administrativeAreaLevelMax) {
                            $this->administrativeAreaLevelMax = $level;
                        }

                    }

                }

            } elseif($address_component->types[0] === 'country') {

                if(!isset($this->country)) {
                    $this->country = new BaseCountry();
                }

                $this->country->code = $address_component->short_name;
                $this->country->name = $address_component->long_name;

            }

        }

        // Set coordinates
        $this->lat = $this->getGoogleMapAPILat($googleApiObject);
        $this->lng = $this->getGoogleMapAPILng($googleApiObject);
        
        // Determine the coordinate type
        $this->setCoordinateType();

        return $this;
    }

    /**
     * Determine the coordinate type
     *
     * @since 1.0
     *
     * @return void
     */
    protected function setCoordinateType() {

        if(isset($this->street->number)) {

            $this->coordinateType = 'street_number';
            $this->street->lat = $this->lat;
            $this->street->lng = $this->lng;

        } elseif(isset($this->street->route)) {

            $this->coordinateType = 'street_name';
            $this->street->lat = $this->lat;
            $this->street->lng = $this->lng;

        } elseif(isset($this->locality->name)) {

            $this->coordinateType = 'locality';
            $this->locality->lat = $this->lat;
            $this->locality->lng = $this->lng;

        } elseif(isset($this->administrativeAreaLevel1->name)) {

            $this->coordinateType = 'administrative_area_level_1';
            $this->administrativeAreaLevel1->lat = $this->lat;
            $this->administrativeAreaLevel1->lng = $this->lng;

        } elseif(isset($this->administrativeAreaLevel2->name)) {

            $this->coordinateType = 'administrative_area_level_2';
            $this->administrativeAreaLevel2->lat = $this->lat;
            $this->administrativeAreaLevel2->lng = $this->lng;

        } elseif(isset($this->administrativeAreaLevel3->name)) {

            $this->coordinateType = 'administrative_area_level_3';
            $this->administrativeAreaLevel3->lat = $this->lat;
            $this->administrativeAreaLevel3->lng = $this->lng;

        } elseif(isset($this->administrativeAreaLevel4->name)) {

            $this->coordinateType = 'administrative_area_level_4';
            $this->administrativeAreaLevel4->lat = $this->lat;
            $this->administrativeAreaLevel4->lng = $this->lng;

        } elseif(isset($this->administrativeAreaLevel5->name)) {

            $this->coordinateType = 'administrative_area_level_5';
            $this->administrativeAreaLevel5->lat = $this->lat;
            $this->administrativeAreaLevel5->lng = $this->lng;

        } elseif(isset($this->country->name)) {

            $this->coordinateType = 'country';
            $this->country->lat = $this->lat;
            $this->country->lng = $this->lng;

        }

    }

    /**
     *
     * @since 1.0
     *
     * @param $googleApiObject
     * @return bool
     */
    public function getGoogleMapAPILat($googleApiObject) {

        if(isset($googleApiObject->geometry->location->lat)) {
            return $googleApiObject->geometry->location->lat;
        } elseif(isset($googleApiObject->geometry->location->k)) {
            return $googleApiObject->geometry->location->k;
        } else {
            return false;
        }

    }

    /**
     *
     * @since 1.0
     *
     * @param $googleApiObject
     * @return bool
     */
    public function getGoogleMapAPILng($googleApiObject) {

        if(isset($googleApiObject->geometry->location->lng)) {
            return $googleApiObject->geometry->location->lng;
        } elseif(isset($googleApiObject->geometry->location->A)) {
            return $googleApiObject->geometry->location->A;
        } elseif(isset($googleApiObject->geometry->location->B)) {
            return $googleApiObject->geometry->location->B;
        } else {
            return false;
        }

    }

    /**
     *
     * @since 1.0
     *
     * @param $language
     * @return void
     */
    public function setLanguage($language) {

        $this->language = $language;

    }

}