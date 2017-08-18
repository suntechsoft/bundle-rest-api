<?php

namespace GlobalGames\Bundle\RestApiBundle\PresentationLayer;

use Symfony\Component\HttpFoundation\Response;

/**
 * Simple bridge between resource representation and application response.
 */
class Presentation implements PresentationInterface
{
    /**
     * @var Response
     */
    private $response;

    /**
     * @var mixed
     */
    private $data;

    /**
     * @return Presentation
     */
    public static function noContent()
    {
        return new static(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param mixed|null $data
     *
     * @return Presentation
     */
    public static function ok($data = null)
    {
        return new static($data, Response::HTTP_OK);
    }

    /**
     * @param mixed|null $data
     * @param int|null   $statusCode
     * @param array      $headers
     */
    public function __construct($data = null, $statusCode = null, array $headers = [])
    {
        $this->data = $data;

        $this->response = new Response();
        $this->response->setStatusCode($statusCode ?: Response::HTTP_OK);
        $this->response->headers->replace($headers);
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
