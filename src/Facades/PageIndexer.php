<?php

namespace Shammaa\LaravelPageIndexer\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array index(string $url, \Shammaa\LaravelPageIndexer\Models\Site $site, string $method = 'both')
 * @method static array bulkIndex(array $urls, \Shammaa\LaravelPageIndexer\Models\Site $site, string $method = 'both')
 * @method static array checkStatus(string $url, \Shammaa\LaravelPageIndexer\Models\Site $site)
 * @method static array syncSites()
 * @method static array syncSitemaps(\Shammaa\LaravelPageIndexer\Models\Site $site)
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

