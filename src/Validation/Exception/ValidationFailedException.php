<?php

namespace GlobalGames\Bundle\RestApiBundle\Validation\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Often happens when validation of resource representation failed.
 */
class ValidationFailedException extends \Exception
{
    /**
     * @var ConstraintViolationListInterface
     */
    private $violationList;

    /**
     * @param string                                $message
     * @param int                                   $code
     * @param \Exception|null                       $previous
     * @param ConstraintViolationListInterface|null $violationList
     */
    public function __construct(
        $message = '',
        $code = 0,
        \Exception $previous = null,
        ConstraintViolationListInterface $violationList = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->violationList = $violationList;
    }

    /**
     * @return ConstraintViolationListInterface
     */
    public function getViolationList()
    {
        return $this->violationList;
    }
}
