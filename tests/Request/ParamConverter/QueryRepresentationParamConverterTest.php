<?php

namespace GlobalGames\Bundle\RestApiBundle\Tests\Request\ParamConverter;

use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use GlobalGames\Bundle\RestApiBundle\Request\ParamConverter\QueryRepresentationParamConverter;
use GlobalGames\Bundle\RestApiBundle\Request\QuerySerializer;
use GlobalGames\Bundle\RestApiBundle\ResourceRepresentation\QueryRepresentationInterface;

/**
 * @codingStandardsIgnoreStart
 */
class QueryRepresentationParamConverterTest extends TestCase
{
    /**
     * @var QueryRepresentationParamConverter
     */
    private $queryRepresentationParamConverter;

    /**
     * @var QuerySerializer|\PHPUnit_Framework_MockObject_MockObject
     */
    private $serializerMock;

    protected function setUp()
    {
        $this->serializerMock = $this
            ->getMockBuilder(QuerySerializer::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->queryRepresentationParamConverter = new QueryRepresentationParamConverter($this->serializerMock);
    }

    public function testSupportsReturnsTrueForQueryRepresentation()
    {
        $supports = $this->queryRepresentationParamConverter->supports(new ParamConverter([
            'class' => QueryRepresentation::class,
        ]));

        $this->assertTrue($supports);
    }

    public function testSupportsReturnsFalseForNotQueryRepresentation()
    {
        $supports = $this->queryRepresentationParamConverter->supports(new ParamConverter([
            'class' => \stdClass::class,
        ]));

        $this->assertFalse($supports);
    }

    public function testApplyConvertsRequestQueryToQueryRepresentation()
    {
        $request = new Request([], [], [], [], [], [], '<some><content /></some>');
        $request->headers->set('Content-Type', 'application/xml');

        $this
            ->serializerMock
            ->expects($this->once())
            ->method('deserialize')
            ->with($request->query, QueryRepresentation::class)
            ->willReturn(new QueryRepresentation())
        ;

        $applied = $this->queryRepresentationParamConverter->apply($request, new ParamConverter([
            'name' => 'queryRepresentation',
            'class' => QueryRepresentation::class,
        ]));

        $this->assertEquals(new QueryRepresentation(), $request->get('queryRepresentation'));
        $this->assertTrue($applied);
    }
}

class QueryRepresentation implements QueryRepresentationInterface
{
}
