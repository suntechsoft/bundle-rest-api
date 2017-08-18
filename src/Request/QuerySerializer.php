<?php

namespace GlobalGames\Bundle\RestApiBundle\Request;

use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Serializes and deserializes query parameters into object.
 */
class QuerySerializer
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Deserializes query into object.
     *
     * @param ParameterBag $query
     * @param string       $class
     *
     * @return object
     */
    public function deserialize(ParameterBag $query, $class)
    {
        // simple "hack" to easy deserialize query with type hinting
        return $this->serializer->deserialize(json_encode($query->all()), $class, 'json');
    }
}
