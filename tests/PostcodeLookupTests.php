<?php

class PostcodeLookupTests extends PHPUnit_Framework_TestCase {

	/**
	 * @var \Lodge\Postcode\Postcode
	 */
	private $postcode;

	/**
	 * Instantiate the postcode class before each test
	 *
	 * @return null
	 */
	public function setUp()
	{
		parent::setUp();

		$this->postcode = new Lodge\Postcode\Postcode();
	}

	/** @test */
	public function it_returns_an_array_with_the_address_coordinates()
	{
		// At the moment there is no way to mock the file_get_contents
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
		$this->assertFalse(strpos(' ', $postcode));
		$this->assertEquals('SW34SZ', $postcode);
	}

	/** @test */
	public function it_returns_the_full_location_for_an_address()
	{
		// Again this is hard to test because of the call to file_get_contents
		$response = $this->postcode->lookup('sw3 4sz');

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
}