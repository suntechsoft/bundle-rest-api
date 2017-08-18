Errors
====== 

We should not invent the wheel again, HTTP provides status codes.

Library supports two layers of errors: 

1. Exceptions; 
2. Validation errors.  

All errors converted to [https://github.com/blongden/vnd.error](vnd.error). To work with them, client 
should send: 

```
Accept: application/vnd.error+json
```

header.

Exceptions
----------

Register error presentation factory which allows to map exception to error presentation: 

```
#!php
<?php 
 
use GlobalGames\Bundle\RestApiBundle\PresentationLayer\ErrorPresentation\Exception\UnsupportedExceptionException;
use GlobalGames\Bundle\RestApiBundle\PresentationLayer\ErrorPresentation;

class SomeConcreteErrorPresentationFactory implements ErrorPresentationFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(\Exception $exception)
    {
        switch (true) {
            case $exception instanceof InvalidActionException:
                return ErrorPresentation::create('invalid action', 500);
            // ...
        }

        throw new UnsupportedExceptionException(sprintf('Unsupported exception type "%s"', get_class($exception)), 0, $exception);
    }
}
```

And register it: 

```
services: 
    some_concrete_error_presentation_factory:
        class: SomeConcreteErrorPresentationFactory
        tags:
            - { name: GlobalGames_rest.error_presentation_factory }
```

Validation errors 
-----------------

Library supports [https://github.com/symfony/validator](Symfony validator component) on resource representations. 

So, if you defined constraints for resource representation: 

```
#!php
<?php 
 
namespace GlobalGames\Bundle\SurveyBuilderBundle\Resource;

use Symfony\Component\Validator\Constraints as Assert;
use GlobalGames\Bundle\RestApiBundle\ResourceRepresentation\ResourceRepresentationInterface;

class CreateSurveyResourceRepresentation implements ResourceRepresentationInterface
{
    /**
     * @JMS\Type("string")
     * @Assert\Length(min = 20)
     *
     * @var string
     */
    public $title;
}
```

Library will automatically validate resource representation and if it's not valid it will return response with 
400 (bad request) error code. 

Under the hood library just throws `ValidationFailedException` with constraint violation list and specific 
error presentation factory converts them to error presentation. 
