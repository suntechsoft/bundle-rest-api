<?php

namespace GlobalGames\Bundle\RestApiBundle\Request\ParamConverter;

use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use GlobalGames\Bundle\RestApiBundle\ResourceRepresentation\ResourceRepresentationInterface;

/**
 * Converts request body to resource representation.
 */
class ResourceRepresentationParamConverter implements ParamConverterInterface
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
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration)
    {
        // supports if parameter is resource representation
        return
            ($configuration instanceof ParamConverter)
            && (null !== $configuration->getClass())
            && is_a($configuration->getClass(), ResourceRepresentationInterface::class, true)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $object = $this->serializer->deserialize($request->getContent(), $configuration->getClass(), $request->getContentType());
        $request->attributes->set($configuration->getName(), $object);

        return true;
    }
}
