<?php

/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\ValidationBundle\EventListener;

use JsonSchema\Constraints\Factory;
use JsonSchema\Validator;
use Sulu\Bundle\ValidationBundle\JsonSchema\CachedSchemaStorage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * This listener checks for every request if a schema was defined for the current route.
 * If so, the requests data is validated by the given schema.
 */
class ValidationRequestListener
{
    /**
     * @var array
     */
    private $schemas;

    /**
     * @var CachedSchemaStorage
     */
    private $schemaStorage;

    /**
     * @param array $schemas
     * @param CachedSchemaStorage $schemaStorage
     */
    public function __construct(array $schemas, CachedSchemaStorage $schemaStorage)
    {
        $this->schemas = $schemas;
        $this->schemaStorage = $schemaStorage;
        $this->schemaStorage->initializeCache();
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $routeId = $request->get('_route');

        if (null === $routeId || !isset($this->schemas[$routeId])) {
            return;
        }

        $data = array_merge($request->request->all(), $request->query->all());
        // FIXME: Validator should also be able to handle array data.
        // https://github.com/sulu/SuluValidationBundle/issues/3
        $dataObject = json_decode(json_encode($data));

        if (!$dataObject) {
            $dataObject = new \stdClass();
        }

        $validator = new Validator(new Factory($this->schemaStorage));
        $validator->check($dataObject, $this->schemaStorage->getSchemaByRoute($routeId));

        if (!$validator->isValid()) {
            $event->setResponse(new Response(json_encode($validator->getErrors()), 400));
        }
    }
}
