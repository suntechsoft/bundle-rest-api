<?php

namespace GlobalGames\Bundle\RestApiBundle\Tests\PresentationLayer;

use GlobalGames\Bundle\RestApiBundle\PresentationLayer\Presentation;
use PHPUnit\Framework\TestCase;

/**
 * @codingStandardsIgnoreStart
 */
class PresentationTest extends TestCase
{
    public function testConstructorCreatesAppropriateResponseAndStoresData()
    {
        $presentation = new Presentation();

        $this->assertNull($presentation->getData());
        $this->assertEquals(200, $presentation->getResponse()->getStatusCode());
    }

    public function testConstructorForwardsStatusAndHeaders()
    {
        $presentation = new Presentation(
            null,
            418,
            ['Content-Type' => 'application/vnd.GlobalGamesforhealth+xml']
        );

        $this->assertNull($presentation->getData());
        $this->assertEquals(418, $presentation->getResponse()->getStatusCode());
        $this->assertEquals('application/vnd.GlobalGamesforhealth+xml', $presentation->getResponse()->headers->get('Content-Type'));
    }

    public function testConstructorStoresData()
    {
        $presentation = new Presentation('any data');

        $this->assertEquals('any data', $presentation->getData());
    }

    public function testNoContentConstructor()
    {
        $presentation = Presentation::noContent();

        $this->assertEquals(204, $presentation->getResponse()->getStatusCode());
        $this->assertEmpty($presentation->getResponse()->getContent());
        $this->assertEmpty($presentation->getData());
    }

    public function testOkConstructor()
    {
        $presentation = Presentation::ok('some data');

        $this->assertEquals(200, $presentation->getResponse()->getStatusCode());
        $this->assertEmpty($presentation->getResponse()->getContent());
        $this->assertEquals('some data', $presentation->getData());
    }
}
