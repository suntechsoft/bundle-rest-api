<?php

namespace GlobalGames\Bundle\RestApiBundle\PresentationLayer\ErrorPresentation;

use GlobalGames\Bundle\RestApiBundle\PresentationLayer\ErrorPresentation;
use GlobalGames\Bundle\RestApiBundle\PresentationLayer\ErrorPresentation\Exception\UnsupportedExceptionException;

/**
 * Maps exceptions to error presentations.
 */
interface ErrorPresentationFactoryInterface
{
    /**
     * Creates error representation based on specified exception.
     *
     * @param \Exception $exception
     *
     * @throws UnsupportedExceptionException
     *
     * @return ErrorPresentation
     */
    public function create(\Exception $exception);
}
