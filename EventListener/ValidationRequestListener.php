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
use JsonSchema\SchemaStorage;
use JsonSchema\Validator;
use Sulu\Bundle\ValidationBundle\Exceptions\SchemaValidationException;
use Symfony\Component\Config\FileLocatorInterface;
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
     * @var FileLocatorInterface
     */
    private $fileLocator;

    /**
     * @param array $schemas
     * @param FileLocatorInterface $fileLocator
     */
    public function __construct(array $schemas, FileLocatorInterface $fileLocator)
    {
        $this->schemas = $schemas;
        $this->fileLocator = $fileLocator;
    }

    /**
     * @param GetResponseEvent $event
     *
     * @throws SchemaValidationException
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        // Check if route is defined.
        $routeId = $request->get('_route');
        if (null === $routeId) {
            return;
        }

        // Check if schema for current route is defined.
        if (!isset($this->schemas[$routeId])) {
            return;
        }

        // Get schema file.
        $schemaFile = $this->fileLocator->locate($this->schemas[$routeId]);
        $schema = json_decode(file_get_contents($schemaFile));

        // Check if json is invalid.
        if ((json_last_error() !== JSON_ERROR_NONE)) {
            $event->setResponse(
                new Response(
                    json_encode(['message' => sprintf('No valid json found in file \'%s\'', $schemaFile)]),
                    500
                )
            );

            return;
        }

        // Create data object from request and query.
        $data = array_merge($request->request->all(), $request->query->all());
        // FIXME: Validator should also be able to handle array data.
        // https://github.com/sulu/SuluValidationBundle/issues/3
        $dataObject = json_decode(json_encode($data));

        if (!$dataObject) {
            $dataObject = new \stdClass();
        }

        $schemaStorage = new SchemaStorage();
        $schemaStorage->addSchema('file://'.$routeId, $schema);
        $validator = new Validator(new Factory($schemaStorage));
        $validator->check($dataObject, $schema);

        // Return error response if data is not valid.
        if (!$validator->isValid()) {
            $event->setResponse(new Response(json_encode($validator->getErrors()), 400));
        }
    }
}
