<?php

namespace Lodge\Postcode;

use RuntimeException;

class ServiceUnavailableException extends RuntimeException
{
    protected $message = 'The service could not be contacted. Please try again.';
    protected $code = 503;
}
