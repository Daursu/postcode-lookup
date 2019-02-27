<?php

namespace Lodge\Postcode\Tests;

use Lodge\Postcode\PostcodeUtils;
use PHPUnit\Framework\TestCase;

class PostcodeUtilsTest extends TestCase
{
    use PostcodeUtils;

    /** @test */
    public function it_mutates_the_postcode_so_that_it_doesnt_contain_spaces()
    {
        $postcode = $this->mutatePostcode('sw3 4sz');

        // It contains uppercase letters and no spaces
        $this->assertFalse(strpos(' ', $postcode));
        $this->assertEquals('SW34SZ', $postcode);
    }
}
