<?php

namespace GlobalGames\Bundle\RestApiBundle\PresentationLayer;

use GlobalGames\Bundle\RestApiBundle\Exception\ResourceNotFoundException;
use GlobalGames\Bundle\RestApiBundle\Exception\RestException;
use PHPUnit\Framework\TestCase;

/**
 * @codingStandardsIgnoreStart
 */
class ResourceNotFoundExceptionTest extends TestCase
{
    public function testResourceNotFoundExceptionIsRestException()
    {
        $this->assertInstanceOf(RestException::class, new ResourceNotFoundException());
    }
}
