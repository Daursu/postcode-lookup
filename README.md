Postcode Lookup
===============
[![Latest Stable Version](https://poser.pugx.org/lodge/postcode-lookup/v/stable.svg)](https://packagist.org/packages/lodge/postcode-lookup) [![Total Downloads](https://poser.pugx.org/lodge/postcode-lookup/downloads.svg)](https://packagist.org/packages/lodge/postcode-lookup) [![Latest Unstable Version](https://poser.pugx.org/lodge/postcode-lookup/v/unstable.svg)](https://packagist.org/packages/lodge/postcode-lookup) [![License](https://poser.pugx.org/lodge/postcode-lookup/license.svg)](https://packagist.org/packages/lodge/postcode-lookup)

PHP library that performs a postcode lookup using Google Maps API.

#### Compatible with Laravel 5.

## Quick start

First of all you need to require the package inside your `composer.json` file:

```
{
    "require": {
        "lodge/postcode-lookup": "0.4.0"
    }
}
```

Note: for Laravel 4 use version `0.3.0`

### Register with Laravel

Register the service provider. Inside your `app.php` config file add:

```php
'providers' => [
	...
	'Lodge\Postcode\PostcodeServiceProvider',
]
```

A facade is also provided, so in order to register it add the following to your `app.php` config file:

```php
'aliases' => [
	...
	'Postcode' => 'Lodge\Postcode\Facades\Postcode',
];
```

Publish the configuration file.
```
php artisan vendor:publish --provider="Lodge\Postcode\PostcodeServiceProvider"
```

Set your Google API key inside `config/postcode.php` file.

## Usage with Laravel

From within your controller you can call:

```php
$address = Postcode::lookup('SW3 4SZ');

// Usage
$address->getPostcode();
$address->getStreetNumber();
$address->getStreet();
$address->getTown(); // -> London
$address->getCounty(); // -> Greater London
$address->getCountry(); // -> United Kingdom
$address->getCoordinates()->getLatitude(); // -> 51.489117499999999
$address->getCoordinates()->getLongitude(); // -> -0.1579016

// $address->toArray();
// Outputs
[
    'postcode'      => 'SW34SZ',
    'street_number' => '',
    'street'        => '',
    'sublocality'   => '',
    'town'          => 'London',
    'county'        => 'Greater London',
    'country'       => 'United Kingdom',
    'latitude'      => 51.489117499999999,
    'longitude'     => -0.1579016
]
```

#### Get the Latitude and Longitude of an address

```php
$coordinates = Postcode::getCoordinates($address);

// Usage
$coordinates->getLatitude();
$coordinates->getLongitude();

// Output
// 51.489117499999999
// -0.1579016
```

## Usage outside of Laravel

```php
// First of all you need to instantiate the class
// And assuming that you have required the composer
// autoload.php file
require 'vendor/autoload.php';

$googleApiKey = 'your-google-api-key';
$postcode = new Lodge\Postcode\Postcode($googleApiKey);
$address = $postcode->lookup('SW3 4SZ');

// Usage
$address->getPostcode();
$address->getStreetNumber();
$address->getStreet();
$address->getTown(); // -> London
$address->getCounty(); // -> Greater London
$address->getCountry(); // -> United Kingdom
$address->getCoordinates()->getLatitude(); // -> 51.489117499999999
$address->getCoordinates()->getLongitude(); // -> -0.1579016

// $address->toArray();
// Outputs
[
    'postcode'      => 'SW34SZ',
    'street_number' => '',
    'street'        => '',
    'sublocality'   => '',
    'town'          => 'London',
    'county'        => 'Greater London',
    'country'       => 'United Kingdom',
    'latitude'      => 51.489117499999999,
    'longitude'     => -0.1579016
]
```

If you need to get just the latitude and longitude for an address you can use:

```php
$googleApiKey = 'your-google-api-key';
$postcode = new Lodge\Postcode\Postcode(new Lodge\Postocde\Gateways\GoogleApi($googleApiKey));
$coordinates = $postcode->getCoordinates('SW3 4SZ');

// Usage
$coordinates->getLatitude();
$coordinates->getLongitude();

// Output
// 51.489117499999999
// -0.1579016
```

## Upgrade from 0.4 to 0.5
* Wrap your calls to `->getCoordinates()` and `->lookup()` in a try catch block
```php
try {
    $address = Postcode::getCoordinates($address)
} catch (AddressNotFoundException $e) {
    // do something
}
```
* The return type for `->getCoordinates()` is now an instance of `Lodge\Postcode\Coordinates` instead of an `array`
* The return type for `->lookup()` is now an instance of `Lodge\Postcode\Address` instead of an `array`
* If you are manually creating a new instance of `Lodge\Postcode\Postcode` then you will need to inject an instance of `GoogleApi`
```php
$postcode = new Lodge\Postcode\Postcode(new Lodge\Postocde\Gateways\GoogleApi($googleApiKey));
```

## Changelog
* **Version 0.5**
    * This version introduces breaking changes
    * Minimum supported PHP version is now 7.1
    * Use an object instead of returning an array
    * Add a gateway interface, so that we can connect to multiple geocode backends
    * Throw exceptions when an address is not found
* **Version 0.4**
    * Added configuration file and the ability to set the Google API key.
    * Updated namespaces to PSR-4
    * Added test stubs.
    * Dropped support for Laravel 4.

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

