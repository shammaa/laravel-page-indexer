<?php

namespace Shammaa\LaravelPageIndexer\Services;

use Illuminate\Support\Facades\Config;

class IndexingManager
{
    public function __construct(
        protected GoogleIndexingService $googleIndexingService,
        protected SearchConsoleService $searchConsoleService,
        protected IndexNowService $indexNowService,
        protected SitemapService $sitemapService
    ) {}

    /**
     * Get site configuration.
     *
     * @return array
     */
    protected function getSiteConfig(): array
    {
        return Config::get('page-indexer.site', []);
    }

    /**
     * Get Google site URL from config.
     *
     * @return string
     */
    protected function getGoogleSiteUrl(): string
    {
        return $this->getSiteConfig()['google_site_url'] ?? '';
    }

    /**
     * Get IndexNow API key from config.
     *
     * @return string|null
     */
    protected function getIndexNowApiKey(): ?string
    {
        $key = $this->getSiteConfig()['indexnow_api_key'] ?? '';
        return !empty($key) ? $key : null;
    }

    /**
     * Check if IndexNow is configured.
     *
     * @return bool
     */
    protected function hasIndexNowKey(): bool
    {
        return !empty($this->getIndexNowApiKey());
    }

    /**
     * Index a single URL.
     *
     * @param string $url
     * @param string $method
     * @return array
     */
    public function index(string $url, string $method = 'both'): array
    {
        $results = [];

        // Submit to Google
        if ($method === 'google' || $method === 'both') {
            $result = $this->googleIndexingService->submitUrl(
                $url,
                'URL_UPDATED'
            );
            $results['google'] = $result;
        }

        // Submit to IndexNow
        if ($method === 'indexnow' || $method === 'both') {
            if ($this->hasIndexNowKey()) {
                $host = $this->sitemapService->extractHost($url);
                $result = $this->indexNowService->submitUrl(
                    $url,
                    $host,
                    $this->getIndexNowApiKey()
                );
                $results['indexnow'] = $result;
            }
        }

        return $results;
    }

    /**
     * Index multiple URLs.
     *
     * @param array $urls
     * @param string $method
     * @return array
     */
    public function bulkIndex(array $urls, string $method = 'both'): array
    {
        $results = [];

        // Submit to Google
        if ($method === 'google' || $method === 'both') {
            $googleUrls = array_slice($urls, 0, 200); // Google limit
            $result = $this->googleIndexingService->submitBulk(
                $googleUrls,
                'URL_UPDATED'
            );
            $results['google'] = $result;
        }

        // Submit to IndexNow
        if ($method === 'indexnow' || $method === 'both') {
            if ($this->hasIndexNowKey()) {
                $host = $this->sitemapService->extractHost($urls[0] ?? '');
                $result = $this->indexNowService->submitBulk(
                    $urls,
                    $host,
                    $this->getIndexNowApiKey()
                );
                $results['indexnow'] = $result;
            }
        }

        return $results;
    }

    /**
     * Check indexing status for a URL.
     *
     * @param string $url
     * @return array
     */
    public function checkStatus(string $url): array
    {
        $siteUrl = $this->getGoogleSiteUrl();
        
        if (empty($siteUrl)) {
            return [
                'success' => false,
                'error' => 'Google site URL not configured. Please set GOOGLE_SITE_URL in your .env file.',
            ];
        }

        return $this->searchConsoleService->inspectUrl($siteUrl, $url);
    }

    /**
     * Sync sites from Google Search Console.
     *
     * @return array
     */
    public function syncSites(): array
    {
        return $this->searchConsoleService->getSites();
    }

    /**
     * Sync sitemaps for the configured site.
     *
     * @return array
     */
    public function syncSitemaps(): array
    {
        $siteUrl = $this->getGoogleSiteUrl();
        
        if (empty($siteUrl)) {
            return [
                'success' => false,
                'error' => 'Google site URL not configured. Please set GOOGLE_SITE_URL in your .env file.',
            ];
        }

        return $this->searchConsoleService->getSitemaps($siteUrl);
    }

    /**
     * Parse and extract URLs from sitemap.
     *
     * @param string $sitemapUrl
     * @return array
     */
    public function parseSitemap(string $sitemapUrl): array
    {
        return $this->sitemapService->parseSitemap($sitemapUrl);
    }
}

