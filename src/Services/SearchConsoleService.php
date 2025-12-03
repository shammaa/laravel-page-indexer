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
    protected function getClient(?string $accessToken = null): Google_Client
    {
        if ($this->client === null) {
            $this->client = new Google_Client();
            $this->client->setClientId($this->config['client_id']);
            $this->client->setClientSecret($this->config['client_secret']);
            $this->client->setRedirectUri($this->config['redirect_uri']);
            $this->client->addScope($this->config['scopes']);
        }

        if ($accessToken) {
            $this->client->setAccessToken($accessToken);
        }

        return $this->client;
    }

    /**
     * Get all sites from Google Search Console.
     *
     * @param string|null $accessToken
     * @return array
     */
    public function getSites(?string $accessToken = null): array
    {
        try {
            $client = $this->getClient($accessToken);
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
     * @param string|null $accessToken
     * @return array
     */
    public function getSitemaps(string $siteUrl, ?string $accessToken = null): array
    {
        try {
            $client = $this->getClient($accessToken);
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
     * @param string|null $accessToken
     * @return array
     */
    public function inspectUrl(string $siteUrl, string $inspectionUrl, ?string $accessToken = null): array
    {
        try {
            $client = $this->getClient($accessToken);
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

