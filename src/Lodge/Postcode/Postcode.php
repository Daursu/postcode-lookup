<?php namespace Lodge\Postcode;

class Postcode {

	public function getCoordinates($address)
	{
		// Sanitize the address:
		$search_code = urlencode($address);

		// Retrieve the latitude and longitude
		$url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $search_code . '&sensor=false';
		$json = json_decode(file_get_contents($url));
		$lat = $json->results[0]->geometry->location->lat;
		$lng = $json->results[0]->geometry->location->lng;

		return array(
			'latitude'  => $lat,
			'longitude' => $lng
		);
	}

	public function lookup($postcode)
	{
		// Sanitize the postcode:
		$coords = $this->getCoordinates($postcode);

		// A second call will now retrieve the address
		$address_url  = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $coords['latitude'] . ',' . $coords['longitude'] . '&sensor=false';
		$address_json = json_decode(file_get_contents($address_url));
		$address_data = $address_json->results[0]->address_components;

		// Initialize all values as empty
		$street_number = '';
		$street        = '';
		$sublocality   = '';
		$town          = '';
		$county        = '';
		$country       = '';

		// Process the return
		foreach ($address_data as $value) {

			if (array_search('street_number', $value->types) !== FALSE)
			{
				$street_number = $value->long_name;
			}
			elseif (array_search('route', $value->types) !== FALSE)
			{
				$street = $value->long_name;
			}
			elseif (array_search('sublocality', $value->types) !== FALSE)
			{
				$sublocality = $value->long_name;
			}
			elseif (array_search('locality', $value->types) !== FALSE)
			{
				$town = $value->long_name;
			}
			elseif (array_search('administrative_area_level_2', $value->types) !== FALSE)
			{
				$county = $value->long_name;
			}
			elseif (array_search('country', $value->types) !== FALSE)
			{
				$country = $value->long_name;
			}
		}

		// Prepare the response
		$array = array(
			'postcode'      => $postcode,
			'street_number' => $street_number,
			'street'        => $street,
			'sublocality'   => $sublocality,
			'town'          => $town,
			'county'        => $county,
			'latitude'      => $coords['latitude'],
			'longitude'     => $coords['longitude']
		);

		return $array;
	}

}