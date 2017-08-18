<?php

namespace GlobalGames\Bundle\RestApiBundle\EventListener;

use JMS\Serializer\Exception\UnsupportedFormatException;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use GlobalGames\Bundle\RestApiBundle\PresentationLayer\ErrorPresentation\ErrorPresentationFactoryInterface;
use GlobalGames\Bundle\RestApiBundle\PresentationLayer\ErrorPresentation\Exception\UnsupportedExceptionException;

/**
 * Transforms exceptions to response. Works only with supported content types.
 */
class ExceptionListener
{
    /**
     * @var array|string[]
     */
    protected static $supportedContentTypeMap = [
        'application/vnd.error+xml' => 'application/xml',
        'application/vnd.error+json' => 'application/json',
    ];

    /**
     * @var ErrorPresentationFactoryInterface
     */
    private $errorPresentationFactory;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var string
     */
    private $debugQueryParameterName;

    /**
     * @param ErrorPresentationFactoryInterface $errorPresentationFactory
     * @param SerializerInterface               $serializer
     * @param string                            $debugQueryParameterName
     */
    public function __construct(
        ErrorPresentationFactoryInterface $errorPresentationFactory,
        SerializerInterface $serializer,
        $debugQueryParameterName
    ) {
        $this->errorPresentationFactory = $errorPresentationFactory;
        $this->serializer = $serializer;
        $this->debugQueryParameterName = $debugQueryParameterName;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $request = $event->getRequest();
        if ($request->query->has($this->debugQueryParameterName)) {
            return;
        }

        try {
            $exception = $event->getException();
            $presentation = $this->errorPresentationFactory->create($exception);

            $acceptableContentTypes = $request->getAcceptableContentTypes();
            foreach ($acceptableContentTypes as $acceptableContentType) {
                if (!isset(static::$supportedContentTypeMap[$acceptableContentType])) {
                    continue;
                }

                try {
                    $acceptableContentType = static::$supportedContentTypeMap[$acceptableContentType];
                    $acceptableFormat = $request->getFormat($acceptableContentType);

                    $serialized = $this->serializer->serialize($presentation->getData(), $acceptableFormat);

                    $response = $presentation->getResponse();
                    $response->setContent($serialized);
                    $response->headers->set('Content-Type', $acceptableContentType);

                    $event->setResponse($response);

                    return;
                } catch (UnsupportedFormatException $e) {
                    continue;
                }
            }
        } catch (UnsupportedExceptionException $e) {
            return;
        }
    }
}
