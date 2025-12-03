<?php

if (!function_exists('page_indexer')) {
    /**
     * Get the PageIndexer service instance.
     *
     * @return \Shammaa\LaravelPageIndexer\Services\IndexingManager
     */
    function page_indexer()
    {
        return app('page-indexer');
    }
}

if (!function_exists('index_page')) {
    /**
     * Index a single page.
     *
     * @param string $url
     * @param \Shammaa\LaravelPageIndexer\Models\Site $site
     * @param string $method
     * @return array
     */
    function index_page(string $url, $site, string $method = 'both')
    {
        return page_indexer()->index($url, $site, $method);
    }
}

if (!function_exists('bulk_index')) {
    /**
     * Index multiple pages.
     *
     * @param array $urls
     * @param \Shammaa\LaravelPageIndexer\Models\Site $site
     * @param string $method
     * @return array
     */
    function bulk_index(array $urls, $site, string $method = 'both')
    {
        return page_indexer()->bulkIndex($urls, $site, $method);
    }
}

