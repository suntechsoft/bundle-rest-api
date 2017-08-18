<?php

namespace GlobalGames\Bundle\RestApiBundle\Tests\Request\ParamConverter;

use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use GlobalGames\Bundle\RestApiBundle\Request\ParamConverter\ResourceRepresentationParamConverter;
use GlobalGames\Bundle\RestApiBundle\ResourceRepresentation\ResourceRepresentationInterface;

/**
 * @codingStandardsIgnoreStart
 */
class ResourceRepresentationParamConverterTest extends TestCase
{
    /**
     * @var ResourceRepresentationParamConverter
     */
    private $resourceRepresentationParamConverter;

    /**
     * @var SerializerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $serializerMock;

    protected function setUp()
    {
        $this->serializerMock = $this
            ->getMockBuilder(SerializerInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->resourceRepresentationParamConverter = new ResourceRepresentationParamConverter($this->serializerMock);
    }

    public function testSupportsReturnsTrueForResourceRepresentation()
    {
        $supports = $this->resourceRepresentationParamConverter->supports(new ParamConverter([
            'class' => ResourceRepresentation::class,
        ]));

        $this->assertTrue($supports);
    }

    public function testSupportsReturnsFalseForNotResourceRepresentation()
    {
        $supports = $this->resourceRepresentationParamConverter->supports(new ParamConverter([
            'class' => \stdClass::class,
        ]));

        $this->assertFalse($supports);
    }

    public function testApplyConvertsRequestBodyToResourceRepresentation()
    {
        $request = new Request([], [], [], [], [], [], '<some><content /></some>');
        $request->headers->set('Content-Type', 'application/xml');

        $this
            ->serializerMock
            ->expects($this->once())
            ->method('deserialize')
            ->with('<some><content /></some>', ResourceRepresentation::class, 'xml')
            ->willReturn(new ResourceRepresentation())
        ;

        $applied = $this->resourceRepresentationParamConverter->apply($request, new ParamConverter([
            'name' => 'resourceRepresentation',
            'class' => ResourceRepresentation::class,
        ]));

        $this->assertEquals(new ResourceRepresentation(), $request->get('resourceRepresentation'));
        $this->assertTrue($applied);
    }
}

class ResourceRepresentation implements ResourceRepresentationInterface
{
}
