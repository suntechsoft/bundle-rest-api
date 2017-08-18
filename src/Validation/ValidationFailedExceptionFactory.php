<?php

namespace GlobalGames\Bundle\RestApiBundle\Validation;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use GlobalGames\Bundle\RestApiBundle\Validation\Exception\ValidationFailedException;

/**
 * Simple factory for exception.
 */
class ValidationFailedExceptionFactory implements ValidationFailedExceptionFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(
        $message = '',
        $code = 0,
        \Exception $previous = null,
        ConstraintViolationListInterface $violationList = null
    ) {
        return new ValidationFailedException($message, $code, $previous, $violationList);
    }
}
