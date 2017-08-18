REST 
====

REST is architectural style, REST is not about API, HTTP or API over HTTP, it's all about representational state transfer. 

But! REST is based on HTTP ideas and perfectly maps to it. [Symfony framework, also, perfectly maps to HTTP](http://symfony.com/doc/current/book/http_fundamentals.html).
So we can use Symfony to implement REST architectural style. 

Core Concepts & Principles 
--------------------------

### Resources & Representations

Resource - is core concept of the REST. Everything in REST revolves around resources. 

“The key abstraction of information in REST is a resource. 
Any information that can be named can be a resource: a document or image, 
a temporal service (e.g. "today's weather in Los Angeles"), 
a collection of other resources, a non-virtual object (e.g. a person), and so on. 
In other words, any concept that might be the target of an author's 
hypertext reference must fit within the definition of a resource.
A resource is a conceptual mapping to a set of entities, not the entity that corresponds to the 
mapping at any particular point in time.” - Roy Fielding’s dissertation.

In REST applications, resources change their state by transferring resource representations. 
Resource can have at least one representation. 
Representations are about different formats and different data structure for the same resource.

You will interact with application through resource representations, so there are some examples: 

```
#!php
<?php 
 
namespace GlobalGames\Bundle\SurveyBuilderBundle\Resource;

use GlobalGames\Bundle\RestApiBundle\ResourceRepresentation\ResourceRepresentationInterface;

class SurveyResourceRepresentation implements ResourceRepresentationInterface
{
    /**
     * @JMS\Type("integer")
     *
     * @var int
     */
    private $id;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $title;

    /**
     * @JMS\Type("boolean")
     *
     * @var bool
     */
    private $active;

    /**
     * @param int $id
     * @param string $title
     */
    public function __construct($id, $title, $active)
    {
        $this->id = $id;
        $this->title = $title;
        $this->active = $active;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }
}
```

Or: 

```
#!php
<?php

namespace GlobalGames\Bundle\SurveyBuilderBundle\Resource;

use GlobalGames\Bundle\RestApiBundle\ResourceRepresentation\ResourceRepresentationInterface;

/**
 * @Hateoas\Relation(
 *     "question",
 *     href = @Hateoas\Route(
 *          "survey_question_get", parameters = {"questionId" = "expr(object.getQuestionId())"}, absolute = true
 *    )
 * )
 */
class SurveyQuestionTypeChangeResourceRepresentation implements ResourceRepresentationInterface
{
    /**
     * @JMS\Type("integer")
     *
     * @var int
     */
    private $questionId;

    /**
     * @JMS\Type("integer")
     *
     * @var string
     */
    private $typeId;

    /**
     * @param int $questionId
     * @param string $typeAlias
     */
    public function __construct($questionId, $typeAlias)
    {
        $this->questionId = $questionId;
        $this->typeId = $typeAlias;
    }

    /**
     * @return int
     */
    public function getQuestionId()
    {
        return $this->questionId;
    }

    /**
     * @return string
     */
    public function getTypeId()
    {
        return $this->typeId;
    }
}
```

All resource representations should implement GlobalGames\Bundle\RestApiBundle\ResourceRepresentation\ResourceRepresentationInterface. 
It allows to use easily to serialize and deserialize them.

You can also handle request query as object. Implement GlobalGames\Bundle\RestApiBundle\ResourceRepresentation\QueryRepresentationInterface
and query parameter bag will be deserialized into object. Validation rules also work here. 

### URI 

Resource can have at least one unique identifier, called Uniform Resource Identifier (URI). 
When we map REST to HTTP, we use URL as URI. 

It's common to practice to use only nouns as URI's and it's more preferable to use plural form to indicate
collection of resources (collection is although resource). 

Example of possible URI's for Survey Designer application:

* https://api.sd.GlobalGamesforhealth.com/surveys - list of surveys;
* https://api.sd.GlobalGamesforhealth.com/surveys/1 - survey with specified id;
* https://api.sd.GlobalGamesforhealth.com/surveys/1/questions - question of survey with specified id;
* https://api.sd.GlobalGamesforhealth.com/surveys/1/questions/1 - question of survey with specified survey id and question id; 
* https://api.sd.GlobalGamesforhealth.com/questions/1 - you could map more than one URI to one resource by protocol and ideology, but better to have one URI, easy to support in the future;
* https://api.sd.GlobalGamesforhealth.com/surveys/1/copyings - copyings of survey;
* https://api.sd.GlobalGamesforhealth.com/surveys/1/questions/1/sortings - question sortings;

```
#!php
<?php 

/**
 * @Route("/", service = "survey_builder.survey_resource_handler")
 */
class SurveyResourceHandler
{
    /**
     * @Route("/surveys", name = "survey_designer_get_surveys")
     * @Method("GET")
     */
    public function getSurveys()
    {
        $surveyResourceRepresentations = ...;
        
        return new Presentation(new CollectionRepresentation($surveyResourceRepresentations));
    }
    
    /**
     * @Route("/surveys/{surveyId}", name = "survey_designer_get_survey")
     * @Method("GET")
     */
    public function getSurvey($surveyId)
    {
        $surveyResourceRepresentation = ...;
        
        return new Presentation($surveyResourceRepresentation);
    }
}
```

Or for process resource representations: 

```
#!php
<?php

/**
 * @Route("/", service = "survey_builder.survey_copying_resource_handler")
 */
class SurveyCopyingResourceHandler
{
    /**
     * @Route("/surveys/{surveyId}/copyings", name = "survey_designer_post_survey_copyings")
     * @Method("POST")
     */
    public function getSurveys($surveyId, SurveyCopyingResourceRepresentation $surveyCopyingResourceRepresentation)
    {
        $copiedSurveyId = $this->copier->copySurvey($surveyId);
        $surveyCopyingResourceRepresentation->setCopiedSurveyId($copiedSurveyId);
        
        return new Presentation($copiedSurveyId);
    }
}
```


### HATEOAS 

HATEOAS (Hypermedia as the Engine of Application State) is simple concept 
about discovering and changing application state through hyperlinks.
 
For examples, you can return list of surveys with next links: 

```
#!xml
<?xml version="1.0" encoding="UTF-8"?>
<collection>
    <entry rel="items">
        <entry>
            <id>1</id>
            <title>
                <![CDATA[Gynaecology Survey]]>
            </title>
            <active>false</active>
            <link rel="self" href="https://api.sd.GlobalGamesforhealth.com/surveys/1"/>
            <link rel="remove" href="https://api.sd.GlobalGamesforhealth.com/surveys/1"/>
            <link rel="questions" href="https://api.sd.GlobalGamesforhealth.com/surveys/1/questions"/>
            <link rel="activation" href="https://api.sd.GlobalGamesforhealth.com/survey/1/activations"/>
            <link rel="copying" href="https://api.sd.GlobalGamesforhealth.com/surveys/1/copyings"/>
        </entry>
        <entry>
            <id>2</id>
            <title>
                <![CDATA[Cancer Survey]]>
            </title>
            <active>true</active>
            <link rel="self" href="https://api.sd.GlobalGamesforhealth.com/surveys/2"/>
            <link rel="remove" href="https://api.sd.GlobalGamesforhealth.com/surveys/2"/>
            <link rel="questions" href="https://api.sd.GlobalGamesforhealth.com/surveys/2/questions"/>
            <link rel="deactivation" href="https://api.sd.GlobalGamesforhealth.com/survey/1/deactivations"/>
            <link rel="copying" href="https://api.sd.GlobalGamesforhealth.com/surveys/1/copyings"/>
        </entry>
    </entry>
</collection>
```

So, client can inspect allowed actions with resources. 

[Excellent library](https://github.com/willdurand/Hateoas) allows us to easy implement this:

```
#!php
<?php

namespace GlobalGames\Bundle\SurveyDesignerBundle\Resource;

use Hateoas\Configuration\Annotation as Hateoas;
use GlobalGames\Bundle\RestApiBundle\ResourceRepresentation\ResourceRepresentationInterface;
use JMS\Serializer\Annotation as JMS;

/**
 * @Hateoas\Relation(
 *     "originalQuestion",
 *     href = @Hateoas\Route(
 *          "survey_question_get", parameters = {"questionId" = "expr(object.getQuestionId())"}, absolute = true
 *    )
 * )
 * @Hateoas\Relation(
 *     "copiedQuestion",
 *     href = @Hateoas\Route(
 *          "survey_question_get", parameters = {"questionId" = "expr(object.getCopiedQuestionId())"}, absolute = true
 *    ),
 *    exclusion = @Hateoas\Exclusion(excludeIf = "expr(object.hasCopiedQuestionId())")
 * )
 */
class SurveyQuestionCopyingResourceRepresentation implements ResourceRepresentationInterface
{
    /**
     * @JMS\Type("integer")
     *
     * @var int
     */
    private $questionId;

    /**
     * @JMS\Type("integer")
     *
     * @var int|null
     */
    private $copiedQuestionId;

    /**
     * SurveyQuestionCopyingResource constructor.
     * @param int $questionId
     * @param int|null $copiedQuestionId
     */
    public function __construct($questionId, $copiedQuestionId)
    {
        $this->questionId = $questionId;
        $this->copiedQuestionId = $copiedQuestionId;
    }

    public function setCopiedQuestionId($copiedQuestionId)
    {
        $this->copiedQuestionId = $copiedQuestionId;
    }

    /**
     * @return int
     */
    public function getQuestionId()
    {
        return $this->questionId;
    }

    /**
     * @return int|null
     */
    public function getCopiedQuestionId()
    {
        return $this->copiedQuestionId;
    }

    /**
     * @return int|null
     */
    public function hasCopiedQuestionId()
    {
        return ($this->getCopiedQuestionId() == null);
    }
}

```

Or: 

```
#!php
<?php

namespace GlobalGames\Bundle\SurveyDesignerBundle\Resource;

use Hateoas\Configuration\Annotation as Hateoas;
use GlobalGames\Bundle\RestApiBundle\ResourceRepresentation\ResourceRepresentationInterface;
use JMS\Serializer\Annotation as JMS;

/**
 * @Hateoas\Relation(
 *     "self",
 *     href = @Hateoas\Route("survey_get", parameters = {"surveyId" = "expr(object.getId())"}, absolute = true)
 * )
 * @Hateoas\Relation(
 *     "activation",
 *     href = @Hateoas\Route("survey_activation_post"),
 *     exclusion = @Hateoas\Exclusion(excludeIf = "expr(object.isActive())")
 * )
 * @Hateoas\Relation(
 *     "deactivation",
 *     href = @Hateoas\Route("survey_activation_delete"),
 *     exclusion = @Hateoas\Exclusion(excludeIf = "expr(!object.isActive())")
 * )
 */
class SurveyResourceRepresentation implements ResourceRepresentationInterface
{
    /**
     * @JMS\Type("integer")
     *
     * @var int
     */
    private $id;

    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $title;

    /**
     * @JMS\Type("boolean")
     *
     * @var bool
     */
    private $active;

    /**
     * @param int $id
     * @param string $title
     */
    public function __construct($id, $title, $active)
    {
        $this->id = $id;
        $this->title = $title;
        $this->active = $active;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }
}
```

### CRUD & Processes

It's not a big deal to model process as resource. 
Read more - https://www.thoughtworks.com/insights/blog/rest-api-design-resource-modeling. 

Literature
----------

1. Definition of REST in Wikipedia -> https://en.wikipedia.org/wiki/Representational_state_transfer
2. Resource modeling -> https://www.thoughtworks.com/insights/blog/rest-api-design-resource-modeling
3. REST in Practice -> http://shop.oreilly.com/product/9780596805838.do
4. Symfony and HTTP Fundamentals - http://symfony.com/doc/current/book/http_fundamentals.html