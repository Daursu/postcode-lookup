<?php

namespace Lodge\Postcode\Gateways;

use Lodge\Postcode\ServiceUnavailableException;

interface GatewayInterface
{
    /**
     * Calls the remote API.
     *
     * @param  string $url
     * @return \stdClass
     * @throws ServiceUnavailableException
     */
    public function fetch($url);
}
