<?php

namespace Shammaa\LaravelPageIndexer\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array submitUrl(string $url, string $host, string $apiKey, string $endpoint = 'bing')
 * @method static array submitBulk(array $urls, string $host, string $apiKey, string $endpoint = 'bing')
 * @method static array submitToMultiple(array $urls, string $host, string $apiKey, array $endpoints = ['bing', 'yandex'])
 *
 * @see \Shammaa\LaravelPageIndexer\Services\IndexNowService
 */
class IndexNow extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \Shammaa\LaravelPageIndexer\Services\IndexNowService::class;
    }
}

