<?php

namespace Shammaa\LaravelPageIndexer\Services;

use Google_Client;
use Google_Service_Indexing;
use Google_Service_Indexing_UrlNotification;
use Illuminate\Support\Facades\Log;

class GoogleIndexingService
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
        }

        return $this->client;
    }

    /**
     * Submit URL for indexing.
     *
     * @param string $url
     * @param string $type 'URL_UPDATED' or 'URL_DELETED'
     * @param string|null $accessToken
     * @return array
     */
    public function submitUrl(string $url, string $type = 'URL_UPDATED'): array
    {
        try {
            $client = $this->getClient();
            $client->fetchAccessTokenWithAssertion();

            $service = new Google_Service_Indexing($client);
            $notification = new Google_Service_Indexing_UrlNotification();
            $notification->setUrl($url);
            $notification->setType($type);

            $response = $service->urlNotifications->publish($notification);

            return [
                'success' => true,
                'url' => $url,
                'notification' => [
                    'urlNotificationMetadata' => [
                        'latestUpdate' => $response->getUrlNotificationMetadata()->getLatestUpdate() ?? null,
                        'url' => $response->getUrlNotificationMetadata()->getUrl() ?? null,
                    ],
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Google Indexing API Error', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'url' => $url,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ];
        }
    }

    /**
     * Submit multiple URLs.
     *
     * @param array $urls
     * @param string $type
     * @param string|null $accessToken
     * @return array
     */
    public function submitBulk(array $urls, string $type = 'URL_UPDATED'): array
    {
        $results = [];

        foreach ($urls as $url) {
            $results[] = $this->submitUrl($url, $type);
            
            // Rate limiting: wait 1 second between requests
            usleep(1000000); // 1 second
        }

        return $results;
    }
}

