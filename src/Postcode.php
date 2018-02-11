<?php

namespace Lodge\Postcode;

class Postcode
{
    /**
     * Google API key.
     *
     * @var string
     */
    protected $apiKey;

    /**
     * Default country to search in.
     *
     * @var string
     */
    protected $country;

    /**
     * Postcode constructor.
     * @param string $apiKey
     */
    public function __construct($apiKey = null)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Retrieves the full address for a given postcode.
     *
     * @param $postcode
     * @return array
     */
    public function lookup($postcode)
    {
        // Mutate the postcode
        $postcode = $this->mutatePostcode($postcode);

        // Sanitize the postcode:
        $coords = $this->getCoordinates($postcode);

        // If empty, we must have no results
        if (empty($coords)) {
            return [];
        }

        // A second call will now retrieve the address
        $address_url  = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $coords['latitude'] . ',' . $coords['longitude'] . '&sensor=false';

        $address_json = $this->callGoogleApi($address_url);

        // The correct result is not always the first one, so loop through results here
        foreach ($address_json->results as $current_address) {
            foreach ($current_address->address_components as $component) {
                // If this address_component is a postcode and matches...
                if(array_search('postal_code', $component->types) !== FALSE
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
            return [];
        }

        // Initialize all values as empty
        $street_number = '';
        $street        = '';
        $sublocality   = '';
        $town          = '';
        $county        = '';
        $country       = '';

        // Process the return
        foreach ($address_data as $value) {
            if (array_search('street_number', $value->types) !== FALSE) {
                $street_number = $value->long_name;
            } elseif (array_search('route', $value->types) !== FALSE) {
                $street = $value->long_name;
            } elseif (array_search('sublocality', $value->types) !== FALSE) {
                $sublocality = $value->long_name;
            } elseif (array_search('locality', $value->types) !== FALSE) {
                $town = $value->long_name;
            } elseif (array_search('administrative_area_level_2', $value->types) !== FALSE) {
                $county = $value->long_name;
            } elseif (array_search('country', $value->types) !== FALSE) {
                $country = $value->long_name;
            }
        }

        return [
            'postcode'      => $postcode,
            'street_number' => $street_number,
            'street'        => $street,
            'sublocality'   => $sublocality,
            'town'          => $town,
            'county'        => $county,
            'country'       => $country,
            'latitude'      => $coords['latitude'],
            'longitude'     => $coords['longitude']
        ];
    }

    /**
     * Returns the lat/lng for the given address.
     *
     * @param  string $address
     * @return array
     */
    public function getCoordinates($address)
    {
        if (! empty($this->country)) {
            $address = trim($address) . ' ' . $this->country;
        }

        // Sanitize the address:
        $search_code = urlencode($address);

        // Retrieve the latitude and longitude
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $search_code . '&sensor=false';

        // If Google Maps API fails, catch it and throw a better error
        $json = $this->callGoogleApi($url);

        if(!empty($json->results)) {
            $lat = $json->results[0]->geometry->location->lat;
            $lng = $json->results[0]->geometry->location->lng;

            return [
                'latitude'  => $lat,
                'longitude' => $lng
            ];
        }

        // We must have nothing if we've got here
        return [];
    }

    /**
     * Sets the default country. This will be appended to the search address.
     *
     * @param  string $country
     * @return $this
     */
    public function setCountry($country = null)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Remove all spaces from postcode.
     *
     * @param $postcode
     * @return string
     */
    public function mutatePostcode($postcode)
    {
        // Ensure the postcode is all upper case with no spaces
        return preg_replace('/ /', '', strtoupper($postcode));
    }

    /**
     * Returns the Google API key.
     *
     * @return null|string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Sets the Google API key.
     *
     * @param  string $apiKey
     * @return $this
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * Calls the Google API.
     *
     * @param  string $url
     * @return mixed
     */
    private function callGoogleApi($url) 
    {
        $url = $this->addApiKeyToUrl($url);

        try {
            $json = json_decode(file_get_contents($url));
        } catch(\Exception $e) {
            throw new ServiceUnavailableException;
        }

        $this->checkApiError($json);

        return $json;
    }

    /**
     * Appends the API key to the url.
     *
     * @param  string $url
     * @return string
     */
    private function addApiKeyToUrl($url)
    {
        return ($api_key = $this->getApiKey())
            ? $url . '&key=' . $api_key
            : $url;
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
