<?php

namespace GlobalGames\Bundle\RestApiBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use GlobalGames\Bundle\RestApiBundle\PresentationLayer\ErrorPresentation\CompositeErrorPresentationFactory;

/**
 * Allows to register error presentation factories in compiler pass.
 */
class ErrorPresentationFactoryCompilerPass implements CompilerPassInterface
{
    /**
     * Composite error presentation factory service identifier.
     */
    const COMPOSITE_ERROR_PRESENTATION_FACTORY_SERVICE_ID = 'globalgames_rest.composite_error_presentation_factory';

    /**
     * Error presentation factory tag.
     */
    const ERROR_PRESENTATION_FACTORY_TAG = 'globalgames_rest.error_presentation_factory';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(self::COMPOSITE_ERROR_PRESENTATION_FACTORY_SERVICE_ID)) {
            return;
        }

        $definition = $container->getDefinition(self::COMPOSITE_ERROR_PRESENTATION_FACTORY_SERVICE_ID);
        if (!is_a($definition->getClass(), CompositeErrorPresentationFactory::class, true)) {
            throw new \RuntimeException(sprintf('"%s" service is not "%s"', self::COMPOSITE_ERROR_PRESENTATION_FACTORY_SERVICE_ID, CompositeErrorPresentationFactory::class));
        }

        $taggedServices = $container->findTaggedServiceIds(self::ERROR_PRESENTATION_FACTORY_TAG);
        $methodCalls = [];
        foreach ($taggedServices as $id => $tags) {
            $methodCalls[] = ['addFactory', [new Reference($id)]];
        }

        $definition->setMethodCalls(array_merge($methodCalls, $definition->getMethodCalls()));
    }
}
