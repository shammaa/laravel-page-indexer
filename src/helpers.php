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

// Direct service usage (without PageIndexer - no database, no config needed)

if (!function_exists('google_indexing')) {
    /**
     * Get GoogleIndexingService instance for direct usage.
     * 
     * Usage: google_indexing()->submitUrl($url)
     * 
     * @return \Shammaa\LaravelPageIndexer\Services\GoogleIndexingService
     */
    function google_indexing()
    {
        return app(\Shammaa\LaravelPageIndexer\Services\GoogleIndexingService::class);
    }
}

if (!function_exists('indexnow')) {
    /**
     * Get IndexNowService instance for direct usage.
     * 
     * Usage: indexnow()->submitUrl($url, $host, $apiKey)
     * 
     * @return \Shammaa\LaravelPageIndexer\Services\IndexNowService
     */
    function indexnow()
    {
        return app(\Shammaa\LaravelPageIndexer\Services\IndexNowService::class);
    }
}

if (!function_exists('submit_to_google')) {
    /**
     * Submit URL directly to Google (without PageIndexer).
     * 
     * Only needs: GOOGLE_SERVICE_ACCOUNT_PATH in .env
     * 
     * @param string $url
     * @param string $type 'URL_UPDATED' or 'URL_DELETED'
     * @return array
     */
    function submit_to_google(string $url, string $type = 'URL_UPDATED'): array
    {
        return google_indexing()->submitUrl($url, $type);
    }
}

if (!function_exists('submit_to_indexnow')) {
    /**
     * Submit URL directly to IndexNow (without PageIndexer).
     * 
     * No config needed - just pass parameters directly.
     * 
     * @param string $url
     * @param string $host Domain host (e.g., 'https://example.com')
     * @param string $apiKey IndexNow API key
     * @param string $endpoint 'bing', 'yandex', 'naver' (default: 'bing')
     * @return array
     */
    function submit_to_indexnow(string $url, string $host, string $apiKey, string $endpoint = 'bing'): array
    {
        return indexnow()->submitUrl($url, $host, $apiKey, $endpoint);
    }
}

