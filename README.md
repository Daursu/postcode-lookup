postcode-lookup
===============

PHP library that performs a postcode lookup using Google Maps API.

## Quick start

This library is intended to be used with composer and laravel.

Inside your app.php config file add

```
'providers' => array(
	...
	'Lodge\Postcode\PostcodeServiceProvider',
)
```

A facade is also provided, so in order to register it add:

```
'aliases' => array(
	...
	'Postcode' => 'Lodge\Postcode\Facades\Postcode',
);
```

## Usage

From within your controller you can call
```
	Postcode::lookup('postcode');
```

This will return an array containing:
* the street name
* the city
* the county
* latitude
* longitude
* postcode

**Update:** added new method:
```
	Postcode::getCoordinates($address);
```
Returns the latitude and longitude as an array:
```
array(
	'latitude'  => 1.521231
	'longitude' => -23.012123
)
```