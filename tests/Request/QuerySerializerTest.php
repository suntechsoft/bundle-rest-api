<?php

namespace GlobalGames\Bundle\RestApiBundle\Tests\Request\ParamConverter;

use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use GlobalGames\Bundle\RestApiBundle\Request\QuerySerializer;
use GlobalGames\Bundle\RestApiBundle\ResourceRepresentation\QueryRepresentationInterface;

/**
 * @codingStandardsIgnoreStart
 */
class QuerySerializerTest extends TestCase
{
    /**
     * @var QuerySerializer
     */
    private $querySerializer;

    /**
     * @var SerializerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $serializerMock;

    protected function setUp()
    {
        $this->serializerMock = $this
            ->getMockBuilder(SerializerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->querySerializer = new QuerySerializer(
            $this->serializerMock
        );
    }

    public function testDeserializeUsesSerializerForDeserialization()
    {
        $this
            ->serializerMock
            ->expects($this->once())
            ->method('deserialize')
            ->with('{"a":1,"b":2}', SomePresentation::class, 'json')
            ->willReturn(new SomePresentation())
        ;

        $object = $this->querySerializer->deserialize(new ParameterBag(['a' => 1, 'b' => 2]), SomePresentation::class);

        $this->assertEquals(new SomePresentation(), $object);
    }
}

class SomePresentation implements QueryRepresentationInterface
{
}
