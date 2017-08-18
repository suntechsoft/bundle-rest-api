<?php

namespace GlobalGames\Bundle\RestApiBundle\Tests\Validation;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use GlobalGames\Bundle\RestApiBundle\PresentationLayer\ErrorPresentation\Exception\UnsupportedExceptionException;
use GlobalGames\Bundle\RestApiBundle\ResourceRepresentation\VndErrorRepresentation;
use GlobalGames\Bundle\RestApiBundle\Validation\Exception\ValidationFailedException;
use GlobalGames\Bundle\RestApiBundle\Validation\ValidationErrorPresentationFactory;

/**
 * @codingStandardsIgnoreStart
 */
class ValidationErrorPresentationFactoryTest extends TestCase
{
    /**
     * @var ValidationErrorPresentationFactory
     */
    private $validationErrorPresentationFactory;

    protected function setUp()
    {
        $this->validationErrorPresentationFactory = new ValidationErrorPresentationFactory();
    }

    public function testItConvertsViolationsToErrorRepresentations()
    {
        $violationList = new ConstraintViolationList([
            new ConstraintViolation('violated message 1', '', [], '', 'path.to.property1', ''),
            new ConstraintViolation('violated message 2', '', [], '', 'path.to.property2', ''),
        ]);
        $errorPresentation = $this->validationErrorPresentationFactory->create(new ValidationFailedException(
            'some message',
            42,
            null,
            $violationList
        ));

        /** @var VndErrorRepresentation $errorRepresentation */
        $errorRepresentation = $errorPresentation->getData();

        $this->assertInstanceOf(VndErrorRepresentation::class, $errorPresentation->getData());
        $this->assertEquals('some message', $errorRepresentation->getMessage());
        $this->assertEquals(400, $errorPresentation->getResponse()->getStatusCode());
        $this->assertEquals(
            [
                new VndErrorRepresentation('violated message 1', null, null, null, 'path.to.property1'),
                new VndErrorRepresentation('violated message 2', null, null, null, 'path.to.property2'),
            ],
            $errorRepresentation->getErrors()
        );
    }

    public function testItSupportsOnlyValidationFailedException()
    {
        $this->expectException(UnsupportedExceptionException::class);

        $this->validationErrorPresentationFactory->create(new \LogicException());

        $this->validationErrorPresentationFactory->create(new ValidationFailedException());
    }
}
