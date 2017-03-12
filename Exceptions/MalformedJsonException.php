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

class MalFormedJsonException extends \Exception
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
