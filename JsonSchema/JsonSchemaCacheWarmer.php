<?php

/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\ValidationBundle\JsonSchema;

use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

class JsonSchemaCacheWarmer implements CacheWarmerInterface
{
    /**
     * @var CachedSchemaStorage
     */
    private $cachedSchemaStorage;

    public function __construct(CachedSchemaStorage $cachedSchemaStorage)
    {
        $this->cachedSchemaStorage = $cachedSchemaStorage;
    }

    public function isOptional()
    {
        return true;
    }

    /**
     * @param string $cacheDir The cache directory
     */
    public function warmUp($cacheDir)
    {
        $this->cachedSchemaStorage->initializeCache();
    }
}
