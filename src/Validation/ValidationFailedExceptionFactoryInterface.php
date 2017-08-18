<?php

namespace GlobalGames\Bundle\RestApiBundle\Validation;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use GlobalGames\Bundle\RestApiBundle\Validation\Exception\ValidationFailedException;

/**
 * Responds for validation failed exception creation.
 */
interface ValidationFailedExceptionFactoryInterface
{
    /**
     * @param string                           $message
     * @param int                              $code
     * @param \Exception                       $previous
     * @param ConstraintViolationListInterface $violationList
     *
     * @return ValidationFailedException
     */
    public function create(
        $message = '',
        $code = 0,
        \Exception $previous = null,
        ConstraintViolationListInterface $violationList = null
    );
}
