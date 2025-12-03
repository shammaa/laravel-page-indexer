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

if (!function_exists('check_indexing_status')) {
    /**
     * Check indexing status for a URL.
     *
     * @param string $url
     * @param \Shammaa\LaravelPageIndexer\Models\Site $site
     * @return array
     */
    function check_indexing_status(string $url, $site)
    {
        return page_indexer()->checkStatus($url, $site);
    }
}

if (!function_exists('is_url_indexed')) {
    /**
     * Check if a URL is indexed (returns boolean).
     *
     * @param string $url
     * @param \Shammaa\LaravelPageIndexer\Models\Site $site
     * @return bool
     */
    function is_url_indexed(string $url, $site): bool
    {
        $result = page_indexer()->checkStatus($url, $site);
        
        if (!$result['success']) {
            return false;
        }

        $inspectionResult = $result['inspectionResult']['indexStatusResult'] ?? null;
        
        if (!$inspectionResult) {
            return false;
        }

        $verdict = $inspectionResult->getVerdict() ?? 'unknown';
        $coverageState = $inspectionResult->getCoverageState() ?? 'unknown';

        return ($verdict === 'PASS' || $coverageState === 'INDEXED');
    }
}

