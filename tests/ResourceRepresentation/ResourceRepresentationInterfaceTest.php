<?php

namespace GlobalGames\Bundle\RestApiBundle\Tests\ResourceRepresentation;

use GlobalGames\Bundle\RestApiBundle\ResourceRepresentation\ResourceRepresentationInterface;
use PHPUnit\Framework\TestCase;

/**
 * @codingStandardsIgnoreStart
 */
class ResourceRepresentationInterfaceTest extends TestCase
{
    public function testInterfaceExistsAndCanBeEasilyImplemented()
    {
        $this->assertInstanceOf(ResourceRepresentationInterface::class, new ResourceRepresentation());
    }
}

/**
 * Stub for testing purposes.
 */
class ResourceRepresentation implements ResourceRepresentationInterface
{
}
