<?php

namespace Lodge\Postcode;

use Lodge\Postcode\Gateways\GatewayInterface;
use Lodge\Postcode\Gateways\GoogleApi;

class Postcode
{
    use PostcodeUtils;

    /**
     * Default country to search in.
     *
     * @var string
     */
    protected $country;

    /**
     * Google API instance.
     *
     * @var GoogleApi
     */
    protected $api;

    /**
     * Postcode constructor.
     *
     * @param \Lodge\Postcode\Gateways\GatewayInterface $apiGateway
     */
    public function __construct(GatewayInterface $apiGateway)
    {
        $this->api = $apiGateway;
    }

    /**
     * Retrieves the full address for a given postcode.
     *
     * @param string $postcode
     * @return \Lodge\Postcode\Address
     * @throws \Lodge\Postcode\Exceptions\AddressNotFoundException
     */
    public function lookup($postcode)
    {
        // Mutate the postcode
        $postcode = $this->mutatePostcode($postcode);

        // Retrieve the lat/lng from the address
        $coords = $this->getCoordinates($postcode);

        // Attempt to reverse geocode the lat/lng
        return $this->api->geocodeLatLng($coords, $postcode);
    }

    /**
     * Returns the lat/lng for the given address.
     *
     * @param  string $address
     * @return \Lodge\Postcode\Coordinates
     * @throws \Lodge\Postcode\Exceptions\AddressNotFoundException
     * @throws \Lodge\Postcode\ServiceUnavailableException
     */
    public function getCoordinates($address)
    {
        if (! empty($this->country)) {
            $address = trim($address) . ' ' . $this->country;
        }

        return $this->api->geocodeFromAddress($address);
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
     * Sets the Google API key.
     *
     * @param  string $apiKey
     * @return $this
     */
    public function setApiKey($apiKey)
    {
        $this->api->setApiKey($apiKey);

        return $this;
    }
}
