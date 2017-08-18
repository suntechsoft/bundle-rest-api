<?php

namespace GlobalGames\Bundle\RestApiBundle\Tests\EventListener;

use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use GlobalGames\Bundle\RestApiBundle\EventListener\ExceptionListener;
use GlobalGames\Bundle\RestApiBundle\PresentationLayer\ErrorPresentation;
use GlobalGames\Bundle\RestApiBundle\PresentationLayer\ErrorPresentation\ErrorPresentationFactoryInterface;
use GlobalGames\Bundle\RestApiBundle\PresentationLayer\ErrorPresentation\Exception\UnsupportedExceptionException;
use GlobalGames\Bundle\RestApiBundle\ResourceRepresentation\VndErrorRepresentation;

/**
 * @codingStandardsIgnoreStart
 */
class ExceptionListenerTest extends TestCase
{
    /**
     * @var ExceptionListener
     */
    private $exceptionListener;

    /**
     * @var ErrorPresentationFactoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $errorPresentationFactory;

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

        $this->errorPresentationFactory = $this
            ->getMockBuilder(ErrorPresentationFactoryInterface::class)
            ->getMock()
        ;

        $this->exceptionListener = new ExceptionListener(
            $this->errorPresentationFactory,
            $this->serializerMock,
            'debug'
        );
    }

    public function testOnKernelExceptionDoesNotSerializeExceptionInRequestWithDebugQueryParameter()
    {
        $request = new Request();
        $request->headers->set('Accept', 'application/xml,application/vnd.error+xml');
        $request->query->set('debug', null);

        /** @var HttpKernelInterface|\PHPUnit_Framework_MockObject_MockObject $kernelMock */
        $kernelMock = $this->getMockBuilder(HttpKernelInterface::class)->getMock();
        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new \InvalidArgumentException('Some invalid argument exception')
        );

        $this
            ->errorPresentationFactory
            ->expects($this->never())
            ->method('create')
            ->withAnyParameters()
        ;

        $this
            ->serializerMock
            ->expects($this->never())
            ->method('serialize')
            ->withAnyParameters()
        ;

        $this
            ->exceptionListener
            ->onKernelException($event)
        ;

        $this->assertFalse($event->isPropagationStopped());
    }

    public function testOnKernelExceptionSerializesException()
    {
        $request = new Request();
        $request->headers->set('Accept', 'application/xml,application/vnd.error+xml');

        /** @var HttpKernelInterface|\PHPUnit_Framework_MockObject_MockObject $kernelMock */
        $kernelMock = $this->getMockBuilder(HttpKernelInterface::class)->getMock();
        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new \InvalidArgumentException('Some invalid argument exception')
        );

        $this
            ->errorPresentationFactory
            ->expects($this->once())
            ->method('create')
            ->with(new \InvalidArgumentException('Some invalid argument exception'))
            ->willReturn(ErrorPresentation::create('Some invalid argument exception', 500))
        ;

        $this
            ->serializerMock
            ->expects($this->once())
            ->method('serialize')
            ->with(new VndErrorRepresentation('Some invalid argument exception'), 'xml')
            ->willReturn('some serialized representation')
        ;

        $this
            ->exceptionListener
            ->onKernelException($event)
        ;

        $this->assertTrue($event->isPropagationStopped());
        $this->assertEquals('application/xml', $event->getResponse()->headers->get('Content-Type'));
        $this->assertEquals('some serialized representation', $event->getResponse()->getContent());
    }

    public function testOnKernelExceptionDoesNotSerializeExceptionIfItIsNotSupportedByFactory()
    {
        $request = new Request();
        $request->headers->set('Accept', 'application/xml,application/vnd.error+xml');

        /** @var HttpKernelInterface|\PHPUnit_Framework_MockObject_MockObject $kernelMock */
        $kernelMock = $this->getMockBuilder(HttpKernelInterface::class)->getMock();
        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new \InvalidArgumentException('Some invalid argument exception')
        );

        $this
            ->errorPresentationFactory
            ->expects($this->once())
            ->method('create')
            ->with(new \InvalidArgumentException('Some invalid argument exception'))
            ->willThrowException(new UnsupportedExceptionException())
        ;

        $this
            ->serializerMock
            ->expects($this->never())
            ->method('serialize')
            ->withAnyParameters()
        ;

        $this
            ->exceptionListener
            ->onKernelException($event)
        ;

        $this->assertFalse($event->isPropagationStopped());
        $this->assertNull($event->getResponse());
    }
}
