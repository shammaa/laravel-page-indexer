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
     * @param string $method
     * @return array
     */
    function index_page(string $url, string $method = 'both')
    {
        return page_indexer()->index($url, $method);
    }
}

if (!function_exists('bulk_index')) {
    /**
     * Index multiple pages.
     *
     * @param array $urls
     * @param string $method
     * @return array
     */
    function bulk_index(array $urls, string $method = 'both')
    {
        return page_indexer()->bulkIndex($urls, $method);
    }
}

if (!function_exists('check_indexing_status')) {
    /**
     * Check indexing status for a URL.
     *
     * @param string $url
     * @return array
     */
    function check_indexing_status(string $url)
    {
        return page_indexer()->checkStatus($url);
    }
}

if (!function_exists('is_url_indexed')) {
    /**
     * Check if a URL is indexed (returns boolean).
     *
     * @param string $url
     * @return bool
     */
    function is_url_indexed(string $url): bool
    {
        $result = page_indexer()->checkStatus($url);
        
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

