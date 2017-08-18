<?php

namespace GlobalGames\Bundle\RestApiBundle\PresentationLayer;

use Symfony\Component\HttpFoundation\Response;

/**
 * Simple bridge between resource representation and application response.
 */
interface PresentationInterface
{
    /**
     * @return mixed
     */
    public function getData();

    /**
     * @return Response
     */
    public function getResponse();
}
