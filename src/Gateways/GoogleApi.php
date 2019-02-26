<?php

namespace Lodge\Postcode\Gateways;

use Lodge\Postcode\Address;
use Lodge\Postcode\Coordinates;
use Lodge\Postcode\Exceptions\AddressNotFoundException;
use Lodge\Postcode\PostcodeUtils;
use Lodge\Postcode\ServiceUnavailableException;

class GoogleApi implements GatewayInterface
{
    use PostcodeUtils;

    /**
     * Google API key.
     *
     * @var string
     */
    protected $apiKey;

    /**
     * GoogleApi constructor.
     * @param null $apiKey
     */
    public function __construct($apiKey = null)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Sets the Google API key.
     *
     * @param  string $key
     * @return $this
     */
    public function setApiKey($key)
    {
        $this->apiKey = $key;

        return $this;
    }

    /**
     * Returns coordinates from the given address.
     *
     * @param  string $address
     * @return \Lodge\Postcode\Coordinates
     * @throws \Lodge\Postcode\Exceptions\AddressNotFoundException
     * @throws \Lodge\Postcode\ServiceUnavailableException
     */
    public function geocodeFromAddress($address)
    {
        // Sanitize the address:
        $search_code = urlencode($address);

        // Retrieve the latitude and longitude
        $url = 'address=' . $search_code . '&sensor=false';

        $json = $this->fetch($url);

        if (!empty($json->results)) {
            $lat = $json->results[0]->geometry->location->lat;
            $lng = $json->results[0]->geometry->location->lng;

            return new Coordinates($lat, $lng);
        }

        throw new AddressNotFoundException($address);
    }

    /**
     * Returns the best address found at the given coordinates.
     *
     * @param \Lodge\Postcode\Coordinates $coordinates
     * @param  string                     $postcode
     * @return \Lodge\Postcode\Address
     * @throws \Lodge\Postcode\Exceptions\AddressNotFoundException
     */
    public function geocodeLatLng(Coordinates $coordinates, $postcode)
    {
        $address_json = $this->fetch(sprintf(
            'latlng=%s,%s&sensor=false',
            $coordinates->getLatitude(),
            $coordinates->getLongitude()
        ));

        // The correct result is not always the first one, so loop through results here
        foreach ($address_json->results as $current_address) {
            foreach ($current_address->address_components as $component) {
                // If this address_component is a postcode and matches...
                if (array_search('postal_code', $component->types) !== FALSE
                    && $postcode == $this->mutatePostcode($component->long_name)
                ) {
                    $address_data = $current_address->address_components;
                    break 2;
                }
            }
        }

        // If no exact match found, use the first as a closest option
        if (!isset($address_data)
            && !empty($address_json->results)
        ) {
            $address_data = $address_json->results[0]->address_components;
        }

        // If still nothing, return empty array
        if (!isset($address_data)) {
            throw new AddressNotFoundException();
        }

        $address = new Address($coordinates, $postcode);

        // Process the return
        foreach ($address_data as $value) {
            if (array_search('street_number', $value->types) !== FALSE) {
                $address->setStreetNumber($value->long_name);
            } elseif (array_search('route', $value->types) !== FALSE) {
                $address->setStreet($value->long_name);
            } elseif (array_search('sublocality', $value->types) !== FALSE) {
                $address->setSublocality($value->long_name);
            } elseif (array_search('locality', $value->types) !== FALSE) {
                $address->setTown($value->long_name);
            } elseif (array_search('administrative_area_level_2', $value->types) !== FALSE) {
                $address->setCounty($value->long_name);
            } elseif (array_search('country', $value->types) !== FALSE) {
                $address->setCountry($value->long_name);
            }
        }

        return $address;
    }

    /**
     * Calls the Google API.
     *
     * @param  string $path
     * @return \stdClass
     * @throws ServiceUnavailableException
     */
    public function fetch($path)
    {
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?'.$path;

        if ($this->apiKey) {
            $url .= '&key='.$this->apiKey;
        }

        try {
            $json = json_decode(file_get_contents($url));
        } catch(\Exception $e) {
            throw new ServiceUnavailableException;
        }

        $this->checkApiError($json);

        return $json;
    }

    /**
     * @param  \stdClass $json
     * @throws ServiceUnavailableException
     */
    private function checkApiError($json)
    {
        if (property_exists($json, 'error_message')) {
            throw new ServiceUnavailableException($json->error_message);
        }
    }
}
