<?php namespace Lodge\Postcode\Facades;

use Illuminate\Support\Facades\Facade;

class Postcode extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'postcode'; }

}