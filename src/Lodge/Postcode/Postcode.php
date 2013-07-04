<?php namespace Lodge\Postcode;

class Postcode {

	public function lookup($postcode)
	{
		// Sanitize the postcode:
		$search_code = urlencode($postcode);

		// Retrieve the latitude and longitude
		$url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $search_code . '&sensor=false';
		$json = json_decode(file_get_contents($url));
		$lat = $json->results[0]->geometry->location->lat;
		$lng = $json->results[0]->geometry->location->lng;

		// A second call will now retrieve the address
		$address_url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $lat . ',' . $lng . '&sensor=false';
		$address_json = json_decode(file_get_contents($address_url));
		$address_data = $address_json->results[0]->address_components;
		$street = str_replace('Dr', 'Drive', $address_data[1]->long_name);
		$town = (isset($address_data[2]))? $address_data[2]->long_name : '';
		$county = (isset($address_data[3])) ? $address_data[3]->long_name : '';

		$array = array('postcode' => $postcode, 'street' => $street, 'town' => $town, 'county' => $county, 'latitude' => $lat, 'longitude' => $lng);
		return $array;
	}

}