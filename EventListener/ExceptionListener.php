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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ExceptionListener
{
    /**
     * Returns status code 400 if a schema validation exception occurs.
     *
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if (!($exception instanceof SchemaValidationException)) {
            return;
        }

        $event->setResponse(new Response($exception->getMessage(), 400));
    }
}
