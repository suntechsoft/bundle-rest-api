<?php

namespace GlobalGames\Bundle\RestApiBundle\Tests\EventListener;

use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use GlobalGames\Bundle\RestApiBundle\EventListener\PresentationResponseListener;
use GlobalGames\Bundle\RestApiBundle\PresentationLayer\Presentation;
use GlobalGames\Bundle\RestApiBundle\ResourceRepresentation\ResourceRepresentationInterface;

/**
 * @codingStandardsIgnoreStart
 */
class PresentationResponseListenerTest extends TestCase
{
    /**
     * @var PresentationResponseListener
     */
    private $presentationResponseListener;

    /**
     * @var SerializerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $serializerMock;

    protected function setUp()
    {
        $this->serializerMock = $this
            ->getMockBuilder(SerializerInterface::class)
            ->getMock()
        ;

        $this->presentationResponseListener = new PresentationResponseListener($this->serializerMock);
    }

    public function testOnKernelViewDoesNotSerializeNothingExceptPresentation()
    {
        /** @var HttpKernelInterface|\PHPUnit_Framework_MockObject_MockObject $kernelMock */
        $kernelMock = $this->getMockBuilder(HttpKernelInterface::class)->getMock();
        $event = new GetResponseForControllerResultEvent($kernelMock, new Request(), HttpKernelInterface::MASTER_REQUEST, new \stdClass());

        $this
            ->serializerMock
            ->expects($this->never())
            ->method('serialize')
        ;

        $this->presentationResponseListener
            ->onKernelView($event)
        ;
    }

    public function testOnKernelViewSerializesPresentation()
    {
        $request = new Request();
        $request->headers->set('Accept', 'application/xml');

        /** @var HttpKernelInterface|\PHPUnit_Framework_MockObject_MockObject $kernelMock */
        $kernelMock = $this->getMockBuilder(HttpKernelInterface::class)->getMock();
        $event = new GetResponseForControllerResultEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Presentation(new ResourceRepresentation())
        );

        $this
            ->serializerMock
            ->expects($this->once())
            ->method('serialize')
            ->with(new ResourceRepresentation(), 'xml')
            ->willReturn('serialized')
        ;

        $this
            ->presentationResponseListener
            ->onKernelView($event)
        ;

        $this->assertTrue($event->isPropagationStopped());
        $this->assertEquals('application/xml', $event->getResponse()->headers->get('Content-Type'));
        $this->assertEquals('serialized', $event->getResponse()->getContent());
    }
}

class ResourceRepresentation implements ResourceRepresentationInterface
{
}
