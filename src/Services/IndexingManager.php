<?php

namespace Shammaa\LaravelPageIndexer\Services;

use Shammaa\LaravelPageIndexer\Models\Page;
use Shammaa\LaravelPageIndexer\Models\Site;

class IndexingManager
{
    public function __construct(
        protected GoogleIndexingService $googleIndexingService,
        protected SearchConsoleService $searchConsoleService,
        protected IndexNowService $indexNowService,
        protected SitemapService $sitemapService
    ) {}

    /**
     * Index a single URL.
     *
     * @param string $url
     * @param Site $site
     * @param string $method
     * @return array
     */
    public function index(string $url, Site $site, string $method = 'both'): array
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
            if ($site->hasIndexNowKey()) {
                $host = $this->sitemapService->extractHost($url);
                $result = $this->indexNowService->submitUrl(
                    $url,
                    $host,
                    $site->indexnow_api_key
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
     * @param Site $site
     * @param string $method
     * @return array
     */
    public function bulkIndex(array $urls, Site $site, string $method = 'both'): array
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
            if ($site->hasIndexNowKey()) {
                $host = $this->sitemapService->extractHost($urls[0] ?? '');
                $result = $this->indexNowService->submitBulk(
                    $urls,
                    $host,
                    $site->indexnow_api_key
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
     * @param Site $site
     * @return array
     */
    public function checkStatus(string $url, Site $site): array
    {
        return $this->searchConsoleService->inspectUrl(
            $site->google_site_url,
            $url
        );
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
     * Sync sitemaps for a site.
     *
     * @param Site $site
     * @return array
     */
    public function syncSitemaps(Site $site): array
    {
        return $this->searchConsoleService->getSitemaps(
            $site->google_site_url
        );
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

