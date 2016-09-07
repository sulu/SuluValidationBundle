<?php

/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\ValidationBundle\Exceptions;

/**
 * Exception that indicates that schema validation was not successful.
 */
class SchemaValidationException extends \Exception
{
    /**
     * @param array $errors
     */
    public function __construct(array $errors)
    {
        parent::__construct(json_encode($errors));
    }
}
