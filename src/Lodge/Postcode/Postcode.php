<?php namespace Lodge\Postcode;

class Postcode {

	public function getCoordinates($address)
	{
		// Sanitize the address:
		$search_code = urlencode($address);

		// Retrieve the latitude and longitude
		$url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $search_code . '&sensor=false';

		// If Google Maps API fails, catch it and throw a better error
		try
		{
			$json = json_decode(file_get_contents($url));
		}
		catch(\Exception $e)
		{
			throw new ServiceUnavailableException;
		}

		if(!empty($json->results))
		{
			$lat = $json->results[0]->geometry->location->lat;
			$lng = $json->results[0]->geometry->location->lng;

			return array(
				'latitude'  => $lat,
				'longitude' => $lng
			);
		}

		// We must have nothing if we've got here
		return array();
	}

	public function mutatePostcode($postcode)
	{
		// Ensure the postcode is all upper case with no spaces
		return preg_replace('/ /', '', strtoupper($postcode));
	}

	public function lookup($postcode)
	{
		// Mutate the postcode
		$postcode = $this->mutatePostcode($postcode);

		// Sanitize the postcode:
		$coords = $this->getCoordinates($postcode);

		// If empty, we must have no results
		if(empty($coords))
			return array();

		// A second call will now retrieve the address
		$address_url  = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $coords['latitude'] . ',' . $coords['longitude'] . '&sensor=false';
		$address_json = json_decode(file_get_contents($address_url));

		// The correct result is not always the first one, so loop through results here
		foreach($address_json->results as $current_address)
		{
			foreach($current_address->address_components as $component)
			{
				// If this address_component is a postcode and matches...
				if(array_search('postal_code', $component->types) !== FALSE &&
				   $postcode == $this->mutatePostcode($component->long_name))
				{
					$address_data = $current_address->address_components;
					break 2;
				}
			}
		}

		// If no exact match found, use the first as a closest option
		if(!isset($address_data) &&
		   !empty($address_json->results))
		{
			$address_data = $address_json->results[0]->address_components;
		}

		// If still nothing, return empty array
		if(!isset($address_data))
			return array();

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
			'country'       => $country,
			'latitude'      => $coords['latitude'],
			'longitude'     => $coords['longitude']
		);

		return $array;
	}

}
