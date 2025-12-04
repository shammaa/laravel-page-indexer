<?php

namespace Shammaa\LaravelPageIndexer\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array index(string $url, string $method = 'both')
 * @method static array bulkIndex(array $urls, string $method = 'both')
 * @method static array checkStatus(string $url)
 * @method static array syncSites()
 * @method static array syncSitemaps()
 * @method static array parseSitemap(string $sitemapUrl)
 *
 * @see \Shammaa\LaravelPageIndexer\Services\IndexingManager
 */
class PageIndexer extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'page-indexer';
    }
}

