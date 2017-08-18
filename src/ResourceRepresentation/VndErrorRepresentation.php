<?php

namespace GlobalGames\Bundle\RestApiBundle\ResourceRepresentation;

use Hateoas\Configuration\Relation;
use Hateoas\Representation\VndErrorRepresentation as HateoasVndErrorRepresentation;
use JMS\Serializer\Annotation as JMS;

/**
 * Represents error in standardized way.
 */
class VndErrorRepresentation extends HateoasVndErrorRepresentation
{
    /**
     * @JMS\Expose
     * @JMS\SerializedName("_embedded")
     *
     * @var array
     */
    private $embedded;

    /**
     * @JMS\Expose
     *
     * @var string|null
     */
    private $path;

    /**
     * @param string                         $message
     * @param string|null                    $logref
     * @param Relation|null                  $help
     * @param Relation|null                  $describes
     * @param string|null                    $path
     * @param VndErrorRepresentation[]|array $errors
     */
    public function __construct($message, $logref = null, Relation $help = null, Relation $describes = null, $path = null, array $errors = [])
    {
        parent::__construct($message, $logref, $help, $describes);

        $this->path = $path;
        $this->embedded['errors'] = $errors;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return array
     */
    public function getEmbedded()
    {
        return $this->embedded;
    }

    /**
     * @return VndErrorRepresentation[]|array
     */
    public function getErrors()
    {
        return $this->embedded['errors'];
    }
}
