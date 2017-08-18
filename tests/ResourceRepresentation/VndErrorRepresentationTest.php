<?php

namespace GlobalGames\Bundle\RestApiBundle\Tests\ResourceRepresentation;

use Hateoas\Representation\VndErrorRepresentation as HateoasVndErrorRepresentation;
use GlobalGames\Bundle\RestApiBundle\ResourceRepresentation\VndErrorRepresentation;
use PHPUnit\Framework\TestCase;

/**
 * @codingStandardsIgnoreStart
 */
class VndErrorRepresentationTest extends TestCase
{
    public function testExtendsHateoasVndErrorRepresentation()
    {
        $this->assertInstanceOf(HateoasVndErrorRepresentation::class, new VndErrorRepresentation('some message'));
    }

    public function testPathPropertyInitialization()
    {
        $errorRepresentation = new VndErrorRepresentation('some message', 'some log ref', null, null, '/somepath');

        $this->assertEquals('/somepath', $errorRepresentation->getPath());
    }

    public function testEmbeddedErrorsInitialization()
    {
        $errorRepresentations = [new VndErrorRepresentation('embedded message', 'embedded log ref', null, null, '/embeddedpath')];
        $errorRepresentation = new VndErrorRepresentation('some message', 'some log ref', null, null, '/somepath', $errorRepresentations);

        $this->assertEquals(
            [
                'errors' => [new VndErrorRepresentation('embedded message', 'embedded log ref', null, null, '/embeddedpath')],
            ],
            $errorRepresentation->getEmbedded()
        );

        $this->assertEquals(
            [
                new VndErrorRepresentation('embedded message', 'embedded log ref', null, null, '/embeddedpath'),
            ],
            $errorRepresentation->getErrors()
        );
    }
}
