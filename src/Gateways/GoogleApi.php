<?php

namespace Lodge\Postcode\Gateways;

use Lodge\Postcode\ServiceUnavailableException;

class GoogleApi implements GatewayInterface
{
    /**
     * Google API key.
     *
     * @var string
     */
    protected $apiKey;

    /**
     * GoogleApi constructor.
     * @param null $apiKey
     */
    public function __construct($apiKey = null)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Sets the Google API key.
     *
     * @param  string $key
     * @return $this
     */
    public function setApiKey($key)
    {
        $this->apiKey = $key;

        return $this;
    }

    /**
     * Calls the Google API.
     *
     * @param  string $url
     * @return \stdClass
     * @throws ServiceUnavailableException
     */
    public function fetch($url)
    {
        if ($this->apiKey) {
            $url .= '&key='.$this->apiKey;
        }

        try {
            $json = json_decode(file_get_contents($url));
        } catch(\Exception $e) {
            throw new ServiceUnavailableException;
        }

        $this->checkApiError($json);

        return $json;
    }

    /**
     * @param  \stdClass $json
     * @throws ServiceUnavailableException
     */
    private function checkApiError($json)
    {
        if (property_exists($json, 'error_message')) {
            throw new ServiceUnavailableException($json->error_message);
        }
    }
}
