<?php

namespace GlobalGames\Bundle\RestApiBundle\Request\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use GlobalGames\Bundle\RestApiBundle\Request\QuerySerializer;
use GlobalGames\Bundle\RestApiBundle\ResourceRepresentation\QueryRepresentationInterface;

/**
 * Converts request query parameters into object.
 */
class QueryRepresentationParamConverter implements ParamConverterInterface
{
    /**
     * @var QuerySerializer
     */
    private $serializer;

    /**
     * @param QuerySerializer $serializer
     */
    public function __construct(QuerySerializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration)
    {
        // supports if parameter is query representation
        return
            ($configuration instanceof ParamConverter)
            && (null !== $configuration->getClass())
            && is_a($configuration->getClass(), QueryRepresentationInterface::class, true)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $object = $this->serializer->deserialize($request->query, $configuration->getClass());
        $request->attributes->set($configuration->getName(), $object);

        return true;
    }
}
