<?php

namespace GlobalGames\Bundle\RestApiBundle\Validation;

use Symfony\Component\Validator\ConstraintViolationInterface;
use GlobalGames\Bundle\RestApiBundle\PresentationLayer\ErrorPresentation;
use GlobalGames\Bundle\RestApiBundle\PresentationLayer\ErrorPresentation\ErrorPresentationFactoryInterface;
use GlobalGames\Bundle\RestApiBundle\ResourceRepresentation\VndErrorRepresentation;
use GlobalGames\Bundle\RestApiBundle\Validation\Exception\ValidationFailedException;

/**
 * Maps violation list to errors.
 */
class ValidationErrorPresentationFactory implements ErrorPresentationFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(\Exception $exception)
    {
        if (!($exception instanceof ValidationFailedException)) {
            throw new ErrorPresentation\Exception\UnsupportedExceptionException();
        }

        $errorRepresentations = [];
        /** @var ConstraintViolationInterface $violation */
        foreach ($exception->getViolationList() as $violation) {
            $errorRepresentations[] = new VndErrorRepresentation(
                $violation->getMessage(),
                null,
                null,
                null,
                $violation->getPropertyPath()
            );
        }

        return new ErrorPresentation(
            new VndErrorRepresentation($exception->getMessage(), null, null, null, null, $errorRepresentations),
            400
        );
    }
}
