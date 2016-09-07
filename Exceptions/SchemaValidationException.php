<?php

namespace Sulu\Bundle\ValidationBundle\Exceptions;

use Exception;

class SchemaValidationException extends Exception
{
    /**
     * @param array $errors
     */
    public function __construct(array $errors)
    {
        parent::__construct(json_encode($errors));
    }
}
