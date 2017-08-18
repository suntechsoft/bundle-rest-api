<?php

namespace GlobalGames\Bundle\RestApiBundle\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use GlobalGames\Bundle\RestApiBundle\DependencyInjection\Compiler\ErrorPresentationFactoryCompilerPass;
use GlobalGames\Bundle\RestApiBundle\RestApiBundle;

/**
 * @codingStandardsIgnoreStart
 */
class RestApiBundleTest extends TestCase
{
    /**
     * @var RestApiBundle
     */
    private $restApiBundle;

    protected function setUp()
    {
        $this->restApiBundle = new RestApiBundle();
    }

    public function testCompilerPassesRegistration()
    {
        $containerBuilder = $this->getMockBuilder(ContainerBuilder::class)->getMock();
        $containerBuilder
            ->expects($this->once())
            ->method('addCompilerPass')
            ->with($this->isInstanceOf(ErrorPresentationFactoryCompilerPass::class))
        ;

        $this->restApiBundle->build($containerBuilder);
    }
}
