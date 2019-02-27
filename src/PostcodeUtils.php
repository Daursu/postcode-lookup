<?php

namespace Lodge\Postcode;

trait PostcodeUtils
{
    /**
     * Remove all spaces from postcode.
     *
     * @param  string $postcode
     * @return string
     */
    public function mutatePostcode($postcode)
    {
        // Ensure the postcode is all upper case with no spaces
        return preg_replace('/ /', '', strtoupper($postcode));
    }
}
