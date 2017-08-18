<?php

namespace GlobalGames\Bundle\RestApiBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use GlobalGames\Bundle\RestApiBundle\ResourceRepresentation\QueryRepresentationInterface;
use GlobalGames\Bundle\RestApiBundle\ResourceRepresentation\ResourceRepresentationInterface;
use GlobalGames\Bundle\RestApiBundle\Validation\Exception\ValidationFailedException;
use GlobalGames\Bundle\RestApiBundle\Validation\ValidationFailedExceptionFactoryInterface;

/**
 * Validates resource representation before handler execution.
 */
class ValidationListener
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var ValidationFailedExceptionFactoryInterface
     */
    private $validationFailedExceptionFactory;

    /**
     * @param ValidatorInterface                        $validator
     * @param ValidationFailedExceptionFactoryInterface $validationFailedExceptionFactory
     */
    public function __construct(
        ValidatorInterface $validator,
        ValidationFailedExceptionFactoryInterface $validationFailedExceptionFactory
    ) {
        $this->validator = $validator;
        $this->validationFailedExceptionFactory = $validationFailedExceptionFactory;
    }

    /**
     * @param FilterControllerEvent $event
     *
     * @throws ValidationFailedException
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        /** @var ResourceRepresentationInterface $resourceRepresentation */
        $resourceRepresentation = null;
        foreach ($event->getRequest()->attributes->all() as $possibleControllerArgument) {
            if (is_object($possibleControllerArgument)
                && ($possibleControllerArgument instanceof ResourceRepresentationInterface || $possibleControllerArgument instanceof QueryRepresentationInterface)) {
                $resourceRepresentation = $possibleControllerArgument;
                continue;
            }
        }

        if (null == $resourceRepresentation) {
            return;
        }

        $violationList = $this->validator->validate($resourceRepresentation);
        if (count($violationList) == 0) {
            return;
        }

        throw $this->validationFailedExceptionFactory->create('Validation failed', 0, null, $violationList);
    }
}
