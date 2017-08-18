<?php

namespace GlobalGames\Bundle\RestApiBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use GlobalGames\Bundle\RestApiBundle\DependencyInjection\Compiler\ErrorPresentationFactoryCompilerPass;

/**
 * Setups bundle.
 */
class RestApiBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ErrorPresentationFactoryCompilerPass());
    }
}
