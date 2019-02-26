<?php

namespace Lodge\Postcode\Tests;

use Lodge\Postcode\Coordinates;
use Lodge\Postcode\Gateways\GoogleApi;
use Lodge\Postcode\Postcode;
use PHPUnit\Framework\TestCase;

class PostcodeLookupTest extends TestCase
{
    /**
     * @var \Lodge\Postcode\Postcode
     */
    private $postcode;

    /** @test */
    public function it_returns_an_array_with_the_address_coordinates()
    {
        // At the moment there is no way to mock the file_get_contents
        $this->googleApiWillReturn(__DIR__.'/stubs/coordinates.json');
        $response = $this->postcode->getCoordinates('SW3 4SZ');

        $this->assertInstanceOf(Coordinates::class, $response);
        $this->assertEquals(51.4891175, $response->getLatitude());
        $this->assertEquals(-0.1579016, $response->getLongitude());
    }

    /** @test */
    public function it_returns_the_full_location_for_an_address()
    {
        $this->googleApiWillReturn(__DIR__.'/stubs/lookup.json');
        $response = $this->postcode->lookup('sw3 4sz');

        $this->assertEquals('SW34SZ', $response->getPostcode());
        $this->assertEquals('Franklins Row', $response->getStreet());
        $this->assertEquals('Greater London', $response->getCounty());
        $this->assertEquals('United Kingdom', $response->getCountry());
        $this->assertEquals(51.4890557, $response->getCoordinates()->getLatitude());
        $this->assertEquals(-0.1579412, $response->getCoordinates()->getLongitude());
    }

    private function googleApiWillReturn($json)
    {
        $googleMock = $this->getMockBuilder(GoogleApi::class)->setMethods(['fetch'])->getMock();
        $googleMock->method('fetch')->willReturn(json_decode(file_get_contents($json)));
        $this->postcode = new Postcode($googleMock);
    }
}
