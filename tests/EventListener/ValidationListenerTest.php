<?php

namespace GlobalGames\Bundle\RestApiBundle\Tests\EventListener;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use GlobalGames\Bundle\RestApiBundle\EventListener\ValidationListener;
use GlobalGames\Bundle\RestApiBundle\ResourceRepresentation\QueryRepresentationInterface;
use GlobalGames\Bundle\RestApiBundle\ResourceRepresentation\ResourceRepresentationInterface;
use GlobalGames\Bundle\RestApiBundle\Validation\Exception\ValidationFailedException;
use GlobalGames\Bundle\RestApiBundle\Validation\ValidationFailedExceptionFactoryInterface;

/**
 * @codingStandardsIgnoreStart
 */
class ValidationListenerTest extends TestCase
{
    /**
     * @var ValidationListener
     */
    private $validationListener;

    /**
     * @var ValidatorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $validator;

    /**
     * @var ValidationFailedExceptionFactoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $validationFailedExceptionFactory;

    protected function setUp()
    {
        $this->validator = $this->getMockBuilder(ValidatorInterface::class)->getMock();
        $this->validationFailedExceptionFactory = $this->getMockBuilder(ValidationFailedExceptionFactoryInterface::class)->getMock();

        $this->validationListener = new ValidationListener(
            $this->validator,
            $this->validationFailedExceptionFactory
        );
    }

    public function testExceptionShouldBeThrowIfThereAreSomeConstraintViolationsOnResourceRepresentation()
    {
        $event = new FilterControllerEvent(
            $this->getMockBuilder(HttpKernelInterface::class)->getMock(),
            function (SomeResourceRepresentation $someParam) {
            },
            new Request([], [], ['someParam' => new SomeResourceRepresentation()]),
            HttpKernelInterface::MASTER_REQUEST
        );

        $this
            ->validator
            ->expects($this->once())
            ->method('validate')
            ->with(new SomeResourceRepresentation())
            ->willReturn(new ConstraintViolationList([new ConstraintViolation('', '', [], '', '', '')]));

        $this
            ->validationFailedExceptionFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn(
                new ValidationFailedException('Validation error', 0, null, new ConstraintViolationList([new ConstraintViolation('', '', [], '', '', '')]))
            );

        $this->expectException(ValidationFailedException::class);

        $this->validationListener->onKernelController($event);
    }

    public function testExceptionShouldBeThrowIfThereAreSomeConstraintViolationsOnQueryRepresentation()
    {
        $event = new FilterControllerEvent(
            $this->getMockBuilder(HttpKernelInterface::class)->getMock(),
            function (SomeQueryRepresentation $someParam) {
            },
            new Request([], [], ['someParam' => new SomeQueryRepresentation()]),
            HttpKernelInterface::MASTER_REQUEST
        );

        $this
            ->validator
            ->expects($this->once())
            ->method('validate')
            ->with(new SomeQueryRepresentation())
            ->willReturn(new ConstraintViolationList([new ConstraintViolation('', '', [], '', '', '')]));

        $this
            ->validationFailedExceptionFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn(
                new ValidationFailedException('Validation error', 0, null, new ConstraintViolationList([new ConstraintViolation('', '', [], '', '', '')]))
            );

        $this->expectException(ValidationFailedException::class);

        $this->validationListener->onKernelController($event);
    }

    public function testExceptionShouldNotBeThrownIfThereAreNoViolations()
    {
        $event = new FilterControllerEvent(
            $this->getMockBuilder(HttpKernelInterface::class)->getMock(),
            function (SomeResourceRepresentation $someParam) {
            },
            new Request([], [], ['someParam' => new SomeResourceRepresentation()]),
            HttpKernelInterface::MASTER_REQUEST
        );

        $this
            ->validator
            ->expects($this->once())
            ->method('validate')
            ->with(new SomeResourceRepresentation())
            ->willReturn(new ConstraintViolationList());

        $this
            ->validationFailedExceptionFactory
            ->expects($this->never())
            ->method('create')
            ->withAnyParameters();

        $this->validationListener->onKernelController($event);
    }

    public function testValidationShouldNotBeInvokedIfThereIsNoResourceRepresentationInRequest()
    {
        $event = new FilterControllerEvent(
            $this->getMockBuilder(HttpKernelInterface::class)->getMock(),
            function () {
            },
            new Request(),
            HttpKernelInterface::MASTER_REQUEST
        );

        $this
            ->validator
            ->expects($this->never())
            ->method('validate')
            ->withAnyParameters();

        $this->validationListener->onKernelController($event);
    }
}

class SomeResourceRepresentation implements ResourceRepresentationInterface
{
}

class SomeQueryRepresentation implements QueryRepresentationInterface
{
}
