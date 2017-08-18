<?php

namespace GlobalGames\Bundle\RestApiBundle\PresentationLayer;

use GlobalGames\Bundle\RestApiBundle\Exception\RestException;
use PHPUnit\Framework\TestCase;

/**
 * @codingStandardsIgnoreStart
 */
class RestExceptionTest extends TestCase
{
    public function testRestExceptionIsException()
    {
        $this->assertInstanceOf(\Exception::class, new RestException());
    }
}
