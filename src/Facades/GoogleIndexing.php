<?php

namespace Shammaa\LaravelPageIndexer\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array submitUrl(string $url, string $type = 'URL_UPDATED')
 * @method static array submitBulk(array $urls, string $type = 'URL_UPDATED')
 *
 * @see \Shammaa\LaravelPageIndexer\Services\GoogleIndexingService
 */
class GoogleIndexing extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \Shammaa\LaravelPageIndexer\Services\GoogleIndexingService::class;
    }
}

