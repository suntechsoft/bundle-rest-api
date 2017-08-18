<?php

namespace GlobalGames\Bundle\RestApiBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\KernelEvents;
use GlobalGames\Bundle\RestApiBundle\DependencyInjection\RestApiExtension;
use GlobalGames\Bundle\RestApiBundle\EventListener\ExceptionListener;
use GlobalGames\Bundle\RestApiBundle\EventListener\PresentationResponseListener;
use GlobalGames\Bundle\RestApiBundle\EventListener\ValidationListener;
use GlobalGames\Bundle\RestApiBundle\PresentationLayer\ErrorPresentation\CompositeErrorPresentationFactory;
use GlobalGames\Bundle\RestApiBundle\Request\ParamConverter\QueryRepresentationParamConverter;
use GlobalGames\Bundle\RestApiBundle\Request\ParamConverter\ResourceRepresentationParamConverter;
use GlobalGames\Bundle\RestApiBundle\Request\QuerySerializer;
use GlobalGames\Bundle\RestApiBundle\Validation\ValidationErrorPresentationFactory;
use GlobalGames\Bundle\RestApiBundle\Validation\ValidationFailedExceptionFactoryInterface;

/**
 * @codingStandardsIgnoreStart
 */
class RestExtensionTest extends TestCase
{
    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    protected function setUp()
    {
        $this->containerBuilder = new ContainerBuilder();

        (new RestApiExtension())->load(['globalgames_for_health_rest' => ['serializer' => ['serialize_null' => true]]], $this->containerBuilder);
    }

    public function testResourceRepresentationParamConverterIsRegistered()
    {
        $this->assertTrue($this->containerBuilder->hasDefinition('globalgames_rest.resource_representation_param_converter'));

        $definition = $this->containerBuilder->getDefinition('globalgames_rest.resource_representation_param_converter');

        $this->assertTrue(is_a($definition->getClass(), ResourceRepresentationParamConverter::class, true));
        $this->assertEquals('jms_serializer.serializer', (string) $definition->getArgument(0));
    }

    public function testQueryRepresentationParamConverterIsRegistered()
    {
        $this->assertTrue($this->containerBuilder->hasDefinition('globalgames_rest.query_representation_param_converter'));

        $definition = $this->containerBuilder->getDefinition('globalgames_rest.query_representation_param_converter');

        $this->assertTrue(is_a($definition->getClass(), QueryRepresentationParamConverter::class, true));
        $this->assertEquals('globalgames_rest.query_serializer', (string) $definition->getArgument(0));
    }

    public function testQuerySerializerIsRegistered()
    {
        $this->assertTrue($this->containerBuilder->hasDefinition('globalgames_rest.query_serializer'));

        $definition = $this->containerBuilder->getDefinition('globalgames_rest.query_serializer');

        $this->assertTrue(is_a($definition->getClass(), QuerySerializer::class, true));
        $this->assertEquals('jms_serializer.serializer', (string) $definition->getArgument(0));
    }

    public function testRepresentationResponseListenerIsRegisteredAndConfigured()
    {
        $this->assertTrue($this->containerBuilder->hasDefinition('globalgames_rest.presentation_response_listener'));

        $definition = $this->containerBuilder->getDefinition('globalgames_rest.presentation_response_listener');

        $this->assertTrue(is_a($definition->getClass(), PresentationResponseListener::class, true));
        $this->assertEquals('jms_serializer.serializer', (string) $definition->getArgument(0));
        $this->assertTrue($definition->getArgument(1));

        $tags = $definition->getTags();
        $this->assertEquals(['kernel.event_listener' => [0 => ['event' => KernelEvents::VIEW, 'method' => 'onKernelView']]], $tags);
    }

    public function testExceptionListenerIsRegistered()
    {
        $this->assertTrue($this->containerBuilder->hasDefinition('globalgames_rest.exception_listener'));

        $definition = $this->containerBuilder->getDefinition('globalgames_rest.exception_listener');

        $this->assertTrue(is_a($definition->getClass(), ExceptionListener::class, true));
        $this->assertEquals('globalgames_rest.composite_error_presentation_factory', (string) $definition->getArgument(0));
        $this->assertEquals('jms_serializer.serializer', (string) $definition->getArgument(1));

        $tags = $definition->getTags();
        $this->assertEquals(['kernel.event_listener' => [0 => ['event' => KernelEvents::EXCEPTION, 'method' => 'onKernelException']]], $tags);
    }

    public function testCompositeErrorPresentationFactoryIsRegistered()
    {
        $this->assertTrue($this->containerBuilder->hasDefinition('globalgames_rest.composite_error_presentation_factory'));

        $definition = $this->containerBuilder->getDefinition('globalgames_rest.composite_error_presentation_factory');

        $this->assertTrue(is_a($definition->getClass(), CompositeErrorPresentationFactory::class, true));
        $this->assertEmpty($definition->getArguments());
    }

    public function testValidationListenerIsRegistered()
    {
        $this->assertTrue($this->containerBuilder->hasDefinition('globalgames_rest.validation_listener'));

        $definition = $this->containerBuilder->getDefinition('globalgames_rest.validation_listener');

        $this->assertTrue(is_a($definition->getClass(), ValidationListener::class, true));
        $this->assertEquals('validator', (string) $definition->getArgument(0));
        $this->assertEquals('globalgames_rest.validation_failed_exception_factory', (string) $definition->getArgument(1));

        $tags = $definition->getTags();
        $this->assertEquals(['kernel.event_listener' => [0 => ['event' => KernelEvents::CONTROLLER, 'method' => 'onKernelController', 'priority' => -256]]], $tags);
    }

    public function testValidationFailedExceptionFactoryIsRegistered()
    {
        $this->assertTrue($this->containerBuilder->hasDefinition('globalgames_rest.validation_failed_exception_factory'));

        $definition = $this->containerBuilder->getDefinition('globalgames_rest.validation_failed_exception_factory');

        $this->assertTrue(is_a($definition->getClass(), ValidationFailedExceptionFactoryInterface::class, true));
    }

    public function testValidationErrorPresentationFactoryIsRegistered()
    {
        $this->assertTrue($this->containerBuilder->hasDefinition('globalgames_rest.validation_error_presentation_factory'));

        $definition = $this->containerBuilder->getDefinition('globalgames_rest.validation_error_presentation_factory');

        $this->assertTrue(is_a($definition->getClass(), ValidationErrorPresentationFactory::class, true));

        $tags = $definition->getTags();
        $this->assertEquals(['globalgames_rest.error_presentation_factory' => [[]]], $tags);
    }
}
