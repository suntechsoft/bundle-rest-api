<?php

namespace GlobalGames\Bundle\RestApiBundle\Tests\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use GlobalGames\Bundle\RestApiBundle\DependencyInjection\Compiler\ErrorPresentationFactoryCompilerPass;
use GlobalGames\Bundle\RestApiBundle\PresentationLayer\ErrorPresentation\CompositeErrorPresentationFactory;

/**
 * @codingStandardsIgnoreStart
 */
class ErrorPresentationFactoryCompilerPassTest extends TestCase
{
    /**
     * @var ErrorPresentationFactoryCompilerPass
     */
    private $errorPresentationFactoryCompilerPass;

    /**
     * @var ContainerBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $containerBuilderMock;

    protected function setUp()
    {
        $this->errorPresentationFactoryCompilerPass = new ErrorPresentationFactoryCompilerPass();

        $this->containerBuilderMock = $this->getMockBuilder(ContainerBuilder::class)->getMock();
    }

    public function testCompilerPassAddsToFactory()
    {
        $definition = new Definition(CompositeErrorPresentationFactory::class);

        $this
            ->containerBuilderMock
            ->expects($this->once())
            ->method('hasDefinition')
            ->with('globalgames_rest.composite_error_presentation_factory')
            ->willReturn(true);

        $this
            ->containerBuilderMock
            ->expects($this->once())
            ->method('getDefinition')
            ->with('globalgames_rest.composite_error_presentation_factory')
            ->willReturn($definition);

        $this
            ->containerBuilderMock
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with('globalgames_rest.error_presentation_factory')
            ->willReturn(['some.error_factory' => ['name' => 'globalgames_rest.error_presentation_factory']]);

        $this->errorPresentationFactoryCompilerPass->process($this->containerBuilderMock);

        $this->assertEquals(
            [
                ['addFactory', [new Reference('some.error_factory')]],
            ],
            $definition->getMethodCalls()
        );
    }

    public function testRunCompilerPassWithoutDefinitionOfCompositeErrorPresentationFactory()
    {
        $this
            ->containerBuilderMock
            ->expects($this->once())
            ->method('hasDefinition')
            ->with('globalgames_rest.composite_error_presentation_factory')
            ->willReturn(false);

        $this
            ->containerBuilderMock
            ->expects($this->never())
            ->method('getDefinition')
            ->with('globalgames_rest.composite_error_presentation_factory');

        $this->errorPresentationFactoryCompilerPass->process($this->containerBuilderMock);
    }

    public function testProcessThrowsExceptionOnWrongDefinitionOfFactory()
    {
        $this
            ->containerBuilderMock
            ->expects($this->once())
            ->method('hasDefinition')
            ->with('globalgames_rest.composite_error_presentation_factory')
            ->willReturn(true);

        $this
            ->containerBuilderMock
            ->expects($this->once())
            ->method('getDefinition')
            ->with('globalgames_rest.composite_error_presentation_factory')
            ->willReturn(new Definition(\stdClass::class));

        $this->expectException(\RuntimeException::class);

        $this->errorPresentationFactoryCompilerPass->process($this->containerBuilderMock);
    }
}
