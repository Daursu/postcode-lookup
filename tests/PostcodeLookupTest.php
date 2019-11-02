<?php

namespace Lodge\Postcode\Tests;

use Lodge\Postcode\GoogleApi;
use Lodge\Postcode\Postcode;
use PHPUnit\Framework\TestCase;

class PostcodeLookupTest extends TestCase
{
    /**
     * @var \Lodge\Postcode\Postcode
     */
    private $postcode;

    /**
     * Instantiate the postcode class before each test
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->postcode = new Postcode();
    }

    /** @test */
    public function it_returns_an_array_with_the_address_coordinates()
    {
        // At the moment there is no way to mock the file_get_contents
        $this->googleApiWillReturn(__DIR__.'/stubs/coordinates.json');
        $response = $this->postcode->getCoordinates('SW3 4SZ');

        $this->assertTrue(is_array($response));
        $this->assertArrayHasKey('latitude', $response);
        $this->assertArrayHasKey('longitude', $response);
    }

    /** @test */
    public function it_mutates_the_postcode_so_that_it_doesnt_contain_spaces()
    {
        $postcode = $this->postcode->mutatePostcode('sw3 4sz');

        // It contains uppercase letters and no spaces
        $this->assertFalse(strpos($postcode, ' '));
        $this->assertEquals('SW34SZ', $postcode);
    }

    /** @test */
    public function it_returns_the_full_location_for_an_address()
    {
        $this->googleApiWillReturn(__DIR__.'/stubs/lookup.json');
        $response = $this->postcode->lookup('sw3 4sz');

        $this->assertEquals('SW34SZ', $response['postcode']);
        $this->assertEquals('Franklins Row', $response['street']);
        $this->assertEquals('Greater London', $response['county']);
        $this->assertEquals('United Kingdom', $response['country']);
        $this->assertEquals(51.4890557, $response['latitude']);
        $this->assertEquals(-0.1579412, $response['longitude']);

        $this->assertArrayHasKey('postcode', $response);
        $this->assertArrayHasKey('street_number', $response);
        $this->assertArrayHasKey('street', $response);
        $this->assertArrayHasKey('sublocality', $response);
        $this->assertArrayHasKey('town', $response);
        $this->assertArrayHasKey('county', $response);
        $this->assertArrayHasKey('country', $response);
        $this->assertArrayHasKey('latitude', $response);
        $this->assertArrayHasKey('longitude', $response);
    }

    private function googleApiWillReturn($json)
    {
        $googleMock = $this->createMock(GoogleApi::class);
        $googleMock->method('fetch')->willReturn(json_decode(file_get_contents($json)));
        $this->postcode->setApi($googleMock);
    }
}
