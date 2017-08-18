<?php

namespace GlobalGames\Bundle\RestApiBundle\PresentationLayer\ErrorPresentation;

use GlobalGames\Bundle\RestApiBundle\PresentationLayer\ErrorPresentation;
use GlobalGames\Bundle\RestApiBundle\PresentationLayer\ErrorPresentation\Exception\UnsupportedExceptionException;
use PHPUnit\Framework\TestCase;

/**
 * @codingStandardsIgnoreStart
 */
class CompositeErrorPresentationFactoryTest extends TestCase
{
    /**
     * @var CompositeErrorPresentationFactory
     */
    private $compositeErrorPresentationFactory;

    protected function setUp()
    {
        $this->compositeErrorPresentationFactory = new CompositeErrorPresentationFactory();
    }

    public function testCreatesAppropriateErrorPresentationForException()
    {
        /** @var ErrorPresentationFactoryInterface|\PHPUnit_Framework_MockObject_MockObject $factory */
        $factory = $this->getMockBuilder(ErrorPresentationFactoryInterface::class)->getMock();
        $factory
            ->expects($this->once())
            ->method('create')
            ->with(new \InvalidArgumentException('some message'))
            ->willReturn(ErrorPresentation::create('some message', 500))
        ;

        $this->compositeErrorPresentationFactory->addFactory($factory);

        $errorPresentation = $this->compositeErrorPresentationFactory->create(new \InvalidArgumentException('some message'));

        $this->assertEquals(ErrorPresentation::create('some message', 500), $errorPresentation);
    }

    public function testCreateThrowsUnsupportedExceptionIfItCanNotCreateAppropriateErrorPresentation()
    {
        $this->expectException(UnsupportedExceptionException::class);

        $this->compositeErrorPresentationFactory->create(new \InvalidArgumentException());
    }

    public function testCreateThrowsUnsupportedExceptionIfNobodyCanNotCreateAppropriateErrorPresentation()
    {
        /** @var ErrorPresentationFactoryInterface|\PHPUnit_Framework_MockObject_MockObject $factory */
        $factory = $this->getMockBuilder(ErrorPresentationFactoryInterface::class)->getMock();
        $factory
            ->expects($this->once())
            ->method('create')
            ->withAnyParameters()
            ->willThrowException(new UnsupportedExceptionException())
        ;

        $this->compositeErrorPresentationFactory->addFactory($factory);

        $this->expectException(UnsupportedExceptionException::class);

        $this->compositeErrorPresentationFactory->create(new \InvalidArgumentException());
    }
}
