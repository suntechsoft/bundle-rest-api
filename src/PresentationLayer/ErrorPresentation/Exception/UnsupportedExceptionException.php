<?php

namespace GlobalGames\Bundle\RestApiBundle\PresentationLayer\ErrorPresentation\Exception;

/**
 * Often happens when factory can not create error presentation based on exception.
 */
class UnsupportedExceptionException extends \Exception
{
}
