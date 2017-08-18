<?php

namespace GlobalGames\Bundle\RestApiBundle\PresentationLayer;

use Symfony\Component\HttpFoundation\Response;
use GlobalGames\Bundle\RestApiBundle\ResourceRepresentation\VndErrorRepresentation;

/**
 * Represents error.
 */
class ErrorPresentation implements PresentationInterface
{
    /**
     * @var Presentation
     */
    private $presentation;

    /**
     * @param string $message
     * @param int    $statusCode
     *
     * @return ErrorPresentation
     */
    public static function create($message, $statusCode)
    {
        return new static(new VndErrorRepresentation($message), $statusCode);
    }

    /**
     * Named constructor for not found error presentation.
     *
     * @param string $message
     *
     * @return ErrorPresentation
     */
    public static function notFound($message = '')
    {
        return static::create($message, Response::HTTP_NOT_FOUND);
    }

    /**
     * Named constructor for bad request error presentation.
     *
     * @param string $message
     *
     * @return ErrorPresentation
     */
    public static function badRequest($message = '')
    {
        return static::create($message, Response::HTTP_BAD_REQUEST);
    }

    /**
     * Named constructor for conflict error presentation.
     *
     * @param string $message
     *
     * @return ErrorPresentation
     */
    public static function conflict($message = '')
    {
        return static::create($message, Response::HTTP_CONFLICT);
    }

    /**
     * @param VndErrorRepresentation $errorRepresentation
     * @param int                    $statusCode
     * @param array                  $headers
     */
    public function __construct(VndErrorRepresentation $errorRepresentation, $statusCode, array $headers = [])
    {
        $this->presentation = new Presentation($errorRepresentation, $statusCode, $headers);
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->presentation->getData();
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->presentation->getResponse();
    }
}
