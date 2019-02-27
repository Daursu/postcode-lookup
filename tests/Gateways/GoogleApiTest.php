<?php

namespace Lodge\Postcode\Tests\Gateways;

use Lodge\Postcode\Coordinates;
use Lodge\Postcode\Exceptions\AddressNotFoundException;
use Lodge\Postcode\Gateways\GoogleApi;
use PHPUnit\Framework\TestCase;

class GoogleApiTest extends TestCase
{
    public function test_it_returns_coordinates()
    {
        /** @var \Lodge\Postcode\Gateways\GoogleApi $gateway */
        $gateway = $this->getGateway();
        $gateway->method('fetch')->willReturn(json_decode(file_get_contents(__DIR__.'/../stubs/coordinates.json')));

        $response = $gateway->geocodeFromAddress('test');
        $this->assertInstanceOf(Coordinates::class, $response);
        $this->assertEquals(51.4891175, $response->getLatitude());
        $this->assertEquals(-0.1579016, $response->getLongitude());
    }

    public function test_it_throws_address_not_found()
    {
        /** @var \Lodge\Postcode\Gateways\GoogleApi $gateway */
        $gateway = $this->getGateway();
        $gateway->method('fetch')->willReturn([]);

        $this->expectException(AddressNotFoundException::class);
        $gateway->geocodeFromAddress('test');
    }

    public function test_it_returns_an_address()
    {
        /** @var \Lodge\Postcode\Gateways\GoogleApi $gateway */
        $gateway = $this->getGateway();
        $gateway->method('fetch')->willReturn(json_decode(file_get_contents(__DIR__.'/../stubs/lookup.json')));
        $response = $gateway->geocodeLatLng(new Coordinates(50, -1), 'sw3 4sz');

        $this->assertEquals('sw3 4sz', $response->getPostcode());
        $this->assertEquals('Franklins Row', $response->getStreet());
        $this->assertEquals('Greater London', $response->getCounty());
        $this->assertEquals('United Kingdom', $response->getCountry());
        $this->assertEquals(50, $response->getCoordinates()->getLatitude());
        $this->assertEquals(-1, $response->getCoordinates()->getLongitude());
    }

    private function getGateway()
    {
        return $this->getMockBuilder(GoogleApi::class)->setMethods(['fetch'])->getMock();
    }
}
