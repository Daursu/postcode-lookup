<?php

namespace Lodge\Postcode\Gateways;

use Lodge\Postcode\Coordinates;

interface GatewayInterface
{
    /**
     * Returns coordinates from the given address.
     *
     * @param  string $address
     * @return \Lodge\Postcode\Coordinates
     * @throws \Lodge\Postcode\Exceptions\AddressNotFoundException
     * @throws \Lodge\Postcode\ServiceUnavailableException
     */
    public function geocodeFromAddress($address);

    /**
     * Returns the best address found at the given coordinates.
     *
     * @param \Lodge\Postcode\Coordinates $coordinates
     * @param  string                     $postcode
     * @return \Lodge\Postcode\Address
     * @throws \Lodge\Postcode\Exceptions\AddressNotFoundException
     */
    public function geocodeLatLng(Coordinates $coordinates, $postcode);
}
