<?php

namespace GlobalGames\Bundle\RestApiBundle\Tests\Validation;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use GlobalGames\Bundle\RestApiBundle\Validation\ValidationFailedExceptionFactory;
use GlobalGames\Bundle\RestApiBundle\Validation\ValidationFailedExceptionFactoryInterface;

/**
 * @codingStandardsIgnoreStart
 */
class ValidationFailedExceptionFactoryTest extends TestCase
{
    /**
     * @var ValidationFailedExceptionFactory
     */
    private $validationFailedExceptionFactory;

    protected function setUp()
    {
        $this->validationFailedExceptionFactory = new ValidationFailedExceptionFactory();
    }

    public function testInstanceOfValidationFailedExceptionFactoryInterface()
    {
        $this->assertInstanceOf(ValidationFailedExceptionFactoryInterface::class, $this->validationFailedExceptionFactory);
    }

    public function testCorrectlyCreatesException()
    {
        $exception = $this
            ->validationFailedExceptionFactory
            ->create(
                'Specific Validation message',
                42,
                $previous = new \LogicException('Some logic message'),
                new ConstraintViolationList([new ConstraintViolation('', '', [], '', '', '')])
            )
        ;

        $this->assertEquals('Specific Validation message', $exception->getMessage());
        $this->assertEquals(42, $exception->getCode());
        $this->assertEquals($previous, $exception->getPrevious());
        $this->assertEquals(new ConstraintViolationList([new ConstraintViolation('', '', [], '', '', '')]), $exception->getViolationList());
    }
}
