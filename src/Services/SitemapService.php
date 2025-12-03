<?php

namespace Shammaa\LaravelPageIndexer\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SitemapService
{
    /**
     * Parse sitemap XML and extract URLs.
     *
     * @param string $sitemapUrl
     * @return array
     */
    public function parseSitemap(string $sitemapUrl): array
    {
        try {
            $response = Http::timeout(30)->get($sitemapUrl);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to fetch sitemap: HTTP ' . $response->status(),
                ];
            }

            $xml = simplexml_load_string($response->body());

            if ($xml === false) {
                return [
                    'success' => false,
                    'error' => 'Failed to parse XML',
                ];
            }

            // Check if it's a sitemap index
            if (isset($xml->sitemap)) {
                return $this->parseSitemapIndex($xml, $sitemapUrl);
            }

            // Regular sitemap
            $urls = [];
            foreach ($xml->url as $url) {
                $urls[] = [
                    'loc' => (string) $url->loc,
                    'lastmod' => isset($url->lastmod) ? (string) $url->lastmod : null,
                    'changefreq' => isset($url->changefreq) ? (string) $url->changefreq : null,
                    'priority' => isset($url->priority) ? (float) $url->priority : null,
                ];
            }

            return [
                'success' => true,
                'type' => 'sitemap',
                'urls' => $urls,
                'count' => count($urls),
            ];
        } catch (\Exception $e) {
            Log::error('Sitemap Parse Error', [
                'sitemapUrl' => $sitemapUrl,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Parse sitemap index (contains multiple sitemaps).
     *
     * @param \SimpleXMLElement $xml
     * @param string $baseUrl
     * @return array
     */
    protected function parseSitemapIndex(\SimpleXMLElement $xml, string $baseUrl): array
    {
        $sitemaps = [];
        
        foreach ($xml->sitemap as $sitemap) {
            $sitemapUrl = (string) $sitemap->loc;
            
            // Recursively parse nested sitemap
            $result = $this->parseSitemap($sitemapUrl);
            
            if ($result['success']) {
                $sitemaps[] = [
                    'url' => $sitemapUrl,
                    'lastmod' => isset($sitemap->lastmod) ? (string) $sitemap->lastmod : null,
                    'urls' => $result['urls'] ?? [],
                    'count' => $result['count'] ?? 0,
                ];
            }
        }

        // Flatten all URLs
        $allUrls = [];
        foreach ($sitemaps as $sitemap) {
            $allUrls = array_merge($allUrls, $sitemap['urls']);
        }

        return [
            'success' => true,
            'type' => 'sitemapindex',
            'sitemaps' => $sitemaps,
            'urls' => $allUrls,
            'count' => count($allUrls),
        ];
    }

    /**
     * Extract domain from URL.
     *
     * @param string $url
     * @return string
     */
    public function extractHost(string $url): string
    {
        $parsed = parse_url($url);
        return $parsed['scheme'] . '://' . $parsed['host'];
    }
}

