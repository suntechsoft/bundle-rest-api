<?php

namespace GlobalGames\Bundle\RestApiBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Loads bundle configuration into DI.
 */
class RestApiExtension extends ConfigurableExtension
{
    /**
     * Presentation response listener service identifier.
     */
    const PRESENTATION_RESPONSE_LISTENER_SERVICE_ID = 'globalgames_rest.presentation_response_listener';

    /**
     * {@inheritdoc}
     */
    protected function loadInternal(array $config, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if ($config['serializer']['serialize_null']) {
            $definition = $container->getDefinition(self::PRESENTATION_RESPONSE_LISTENER_SERVICE_ID);
            $definition->replaceArgument(1, true);
        }
    }
}
