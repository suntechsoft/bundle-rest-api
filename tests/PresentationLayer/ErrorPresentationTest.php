<?php

namespace GlobalGames\Bundle\RestApiBundle\Tests\PresentationLayer;

use GlobalGames\Bundle\RestApiBundle\PresentationLayer\ErrorPresentation;
use GlobalGames\Bundle\RestApiBundle\PresentationLayer\PresentationInterface;
use GlobalGames\Bundle\RestApiBundle\ResourceRepresentation\VndErrorRepresentation;
use PHPUnit\Framework\TestCase;

/**
 * @codingStandardsIgnoreStart
 */
class ErrorPresentationTest extends TestCase
{
    public function testCreateConstructor()
    {
        $presentation = ErrorPresentation::create('some message', 400);

        $this->assertEquals('some message', $presentation->getData()->getMessage());
        $this->assertEquals(400, $presentation->getResponse()->getStatusCode());
    }

    public function testNotFoundConstructor()
    {
        $presentation = ErrorPresentation::notFound('some message');

        $this->assertEquals('some message', $presentation->getData()->getMessage());
        $this->assertEquals(404, $presentation->getResponse()->getStatusCode());
    }

    public function testBadRequestConstructor()
    {
        $presentation = ErrorPresentation::badRequest('some message');

        $this->assertEquals('some message', $presentation->getData()->getMessage());
        $this->assertEquals(400, $presentation->getResponse()->getStatusCode());
    }

    public function testConflictConstructor()
    {
        $presentation = ErrorPresentation::conflict('some message');

        $this->assertEquals('some message', $presentation->getData()->getMessage());
        $this->assertEquals(409, $presentation->getResponse()->getStatusCode());
    }

    public function testConstructor()
    {
        $presentation = new ErrorPresentation(new VndErrorRepresentation('some message'), 200, ['Content-Type' => 'application/vnd.error+xml']);

        $this->assertEquals(new VndErrorRepresentation('some message'), $presentation->getData());
        $this->assertEquals(200, $presentation->getResponse()->getStatusCode());
        $this->assertEquals('application/vnd.error+xml', $presentation->getResponse()->headers->get('Content-Type'));
    }

    public function testImplementsPresentationInterface()
    {
        $this->assertInstanceOf(PresentationInterface::class, ErrorPresentation::create('some message', 400));
    }
}
