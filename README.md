Postcode Lookup
===============
[![Latest Stable Version](https://poser.pugx.org/lodge/postcode-lookup/v/stable.svg)](https://packagist.org/packages/lodge/postcode-lookup) [![Total Downloads](https://poser.pugx.org/lodge/postcode-lookup/downloads.svg)](https://packagist.org/packages/lodge/postcode-lookup) [![Latest Unstable Version](https://poser.pugx.org/lodge/postcode-lookup/v/unstable.svg)](https://packagist.org/packages/lodge/postcode-lookup) [![License](https://poser.pugx.org/lodge/postcode-lookup/license.svg)](https://packagist.org/packages/lodge/postcode-lookup)

PHP library that performs a postcode lookup using Google Maps API.

#### Compatible with Laravel 4 & 5.

## Quick start

First of all you need to require the package inside your `composer.json` file:

```
{
    "require": {
        "lodge/postcode-lookup": "dev-master"
    }
}
```

### Register with Laravel

Register the service provider. Inside your `app.php` config file add:

```
'providers' => array(
	...
	'Lodge\Postcode\PostcodeServiceProvider',
)
```

A facade is also provided, so in order to register it add the following to your `app.php` config file:

```
'aliases' => array(
	...
	'Postcode' => 'Lodge\Postcode\Facades\Postcode',
);
```

## Usage with Laravel

From within your controller you can call:

```
	$postcode = Postcode::lookup('SW3 4SZ');

	print_r($postcode);

	// Outputs
	array(
		'postcode'      => 'SW34SZ',
		'street_number' => '',
		'street'        => '',
		'sublocality'   => '',
		'town'          => 'London',
		'county'        => 'Greater London',
		'country'       => 'United Kingdom',
		'latitude'      => 51.489117499999999,
		'longitude'     => -0.1579016
	);
```

#### Get the Latitude and Longitude of an address

```
	$coordinates = Postcode::getCoordinates($address);

	print_r($coordinates);

	// Outputs
	array(
		'latitude'  => 1.521231
		'longitude' => -23.012123
	)

```

## Usage outside of Laravel

```
	// First of all you need to instantiate the class
	// And assuming that you have required the composer
	// autoload.php file
	require 'vendor/autoload.php';

	$postcode = new Lodge\Postcode\Postcode();
	$results = $postcode->lookup('SW3 4SZ');

	print_r($results);

	// Outputs
	array(
		'postcode'      => 'SW34SZ',
		'street_number' => '',
		'street'        => '',
		'sublocality'   => '',
		'town'          => 'London',
		'county'        => 'Greater London',
		'country'       => 'United Kingdom',
		'latitude'      => 51.489117499999999,
		'longitude'     => -0.1579016
	)
```

If you need to get just the latitude and longitude for an address you can use:

```
	$postcode = new Lodge\Postcode\Postcode();
	$results = $postcode->getCoordinates('SW3 4SZ');

	print_r($results);

	// Outputs
	array(
		'latitude'  => 51.489117499999999
		'longitude' => -0.1579016
	)
```

## Changelog

* **Version 0.3**
	* Removed deprecated `bind` call and instantiate as a singleton instead.

* **Version 0.2**
	* Laravel 5 compatible
	* Fixed facade namespace, that would not adhere to PSR-0
	* Updated documentation

* **Version 0.1**
 	* Initial Release

## License

The MIT License (MIT)

Copyright (c) 2013-2015 Dan Ursu

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

