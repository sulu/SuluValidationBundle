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

use Sulu\Bundle\ValidationBundle\Exceptions\SchemaValidationException;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

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

        // Create data object from request and query.
        $data = array_merge($request->request->all(), $request->query->all());
        $dataObject = json_decode(json_encode($data));

        // Validate data with given schema.
        $validator = new \JsonSchema\Validator();
        $validator->check($dataObject, $schema);

        if (!$validator->isValid()) {
            throw new SchemaValidationException($validator->getErrors());
        }
    }
}
