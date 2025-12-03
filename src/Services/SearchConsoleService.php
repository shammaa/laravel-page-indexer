<?php

namespace Shammaa\LaravelPageIndexer\Services;

use Google_Client;
use Google_Service_Webmasters;
use Illuminate\Support\Facades\Log;

class SearchConsoleService
{
    protected array $config;
    protected ?Google_Client $client = null;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Get authenticated Google Client.
     */
    protected function getClient(): Google_Client
    {
        if ($this->client === null) {
            $this->client = new Google_Client();
            
            if (!file_exists($this->config['service_account_path'])) {
                throw new \RuntimeException(
                    'Google Service Account file not found at: ' . $this->config['service_account_path'] .
                    '. Please set GOOGLE_SERVICE_ACCOUNT_PATH in your .env file.'
                );
            }
            
            $this->client->setAuthConfig($this->config['service_account_path']);
            $this->client->addScope($this->config['scopes']);
            $this->client->fetchAccessTokenWithAssertion();
        }

        return $this->client;
    }

    /**
     * Get all sites from Google Search Console.
     *
     * @return array
     */
    public function getSites(): array
    {
        try {
            $client = $this->getClient();
            $service = new Google_Service_Webmasters($client);
            $sites = $service->sites->listSites();

            $result = [];
            foreach ($sites as $site) {
                $result[] = [
                    'siteUrl' => $site->getSiteUrl(),
                    'permissionLevel' => $site->getPermissionLevel(),
                ];
            }

            return [
                'success' => true,
                'sites' => $result,
            ];
        } catch (\Exception $e) {
            Log::error('Search Console API Error', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get sitemaps for a site.
     *
     * @param string $siteUrl
     * @return array
     */
    public function getSitemaps(string $siteUrl): array
    {
        try {
            $client = $this->getClient();
            $service = new Google_Service_Webmasters($client);
            $sitemaps = $service->sitemaps->listSitemaps($siteUrl);

            $result = [];
            foreach ($sitemaps as $sitemap) {
                $result[] = [
                    'path' => $sitemap->getPath(),
                    'contents' => $sitemap->getContents() ?? [],
                    'errors' => $sitemap->getErrors() ?? 0,
                    'warnings' => $sitemap->getWarnings() ?? 0,
                    'isPending' => $sitemap->getIsPending() ?? false,
                    'isSitemapsIndex' => $sitemap->getIsSitemapsIndex() ?? false,
                    'lastDownloaded' => $sitemap->getLastDownloaded() ?? null,
                    'lastSubmitted' => $sitemap->getLastSubmitted() ?? null,
                ];
            }

            return [
                'success' => true,
                'sitemaps' => $result,
            ];
        } catch (\Exception $e) {
            Log::error('Search Console Sitemaps API Error', [
                'siteUrl' => $siteUrl,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Inspect URL indexing status.
     *
     * @param string $siteUrl
     * @param string $inspectionUrl
     * @return array
     */
    public function inspectUrl(string $siteUrl, string $inspectionUrl): array
    {
        try {
            $client = $this->getClient();
            $service = new Google_Service_Webmasters($client);

            $inspectionRequest = new \Google_Service_Webmasters_UrlInspection_Index_Request();
            $inspectionRequest->setInspectionUrl($inspectionUrl);
            $inspectionRequest->setSiteUrl($siteUrl);

            $response = $service->urlInspection_index->inspect($inspectionRequest);

            return [
                'success' => true,
                'inspectionResult' => [
                    'indexStatusResult' => $response->getIndexStatusResult() ?? null,
                    'ampResult' => $response->getAmpResult() ?? null,
                    'mobileUsabilityResult' => $response->getMobileUsabilityResult() ?? null,
                    'richResultsResult' => $response->getRichResultsResult() ?? null,
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Search Console URL Inspection Error', [
                'siteUrl' => $siteUrl,
                'inspectionUrl' => $inspectionUrl,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}

