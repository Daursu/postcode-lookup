<?php

namespace Lodge\Postcode;

class Address
{
    /**
     * @var string
     */
    protected $postcode;

    /**
     * @var string
     */
    protected $streetNumber;

    /**
     * @var string
     */
    protected $street;

    /**
     * @var string
     */
    protected $sublocality;

    /**
     * @var string
     */
    protected $town;

    /**
     * @var string
     */
    protected $county;

    /**
     * @var string
     */
    protected $country;

    /**
     * @var \Lodge\Postcode\Coordinates
     */
    protected $coordinates;

    /**
     * Address constructor.
     *
     * @param \Lodge\Postcode\Coordinates $coordinates
     * @param string                      $postcode
     */
    public function __construct(Coordinates $coordinates, $postcode)
    {
        $this->coordinates = $coordinates;
        $this->postcode = $postcode;
    }

    /**
     * @return string
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * @return string
     */
    public function getStreetNumber()
    {
        return $this->streetNumber;
    }

    /**
     * @param string $streetNumber
     */
    public function setStreetNumber($streetNumber)
    {
        $this->streetNumber = $streetNumber;
    }

    /**
     * @param string $county
     */
    public function setCounty($county)
    {
        $this->county = $county;
    }

    /**
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param string $street
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * @return string
     */
    public function getSublocality()
    {
        return $this->sublocality;
    }

    /**
     * @param string $sublocality
     */
    public function setSublocality($sublocality)
    {
        $this->sublocality = $sublocality;
    }

    /**
     * @return string
     */
    public function getTown()
    {
        return $this->town;
    }

    /**
     * @param string $town
     */
    public function setTown($town)
    {
        $this->town = $town;
    }

    /**
     * @return string
     */
    public function getCounty()
    {
        return $this->county;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return \Lodge\Postcode\Coordinates
     */
    public function getCoordinates()
    {
        return $this->coordinates;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'postcode'      => $this->postcode,
            'street_number' => $this->streetNumber,
            'street'        => $this->street,
            'sublocality'   => $this->sublocality,
            'town'          => $this->town,
            'county'        => $this->county,
            'country'       => $this->country,
            'latitude'      => $this->coordinates->getLatitude(),
            'longitude'     => $this->coordinates->getLongitude(),
        ];
    }
}
