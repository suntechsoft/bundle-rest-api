<?php

namespace GlobalGames\Bundle\RestApiBundle\PresentationLayer\ErrorPresentation;

use GlobalGames\Bundle\RestApiBundle\PresentationLayer\ErrorPresentation\Exception\UnsupportedExceptionException;

/**
 * Represents factories as single factory.
 */
class CompositeErrorPresentationFactory implements ErrorPresentationFactoryInterface
{
    /**
     * @var ErrorPresentationFactoryInterface[]
     */
    private $errorPresentationFactories = [];

    /**
     * @param ErrorPresentationFactoryInterface $errorPresentationFactory
     *
     * @return CompositeErrorPresentationFactory
     */
    public function addFactory(ErrorPresentationFactoryInterface $errorPresentationFactory)
    {
        $this->errorPresentationFactories[] = $errorPresentationFactory;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function create(\Exception $exception)
    {
        foreach ($this->errorPresentationFactories as $errorPresentationFactory) {
            try {
                return $errorPresentationFactory->create($exception);
            } catch (UnsupportedExceptionException $e) {
                continue;
            }
        }

        throw new UnsupportedExceptionException(sprintf('Unsupported exception type "%s"', get_class($exception)), 0, $exception);
    }
}
