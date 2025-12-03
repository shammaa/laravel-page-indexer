<?php

namespace Shammaa\LaravelPageIndexer\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IndexNowService
{
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Submit URL to IndexNow (Bing, Yandex, etc.).
     *
     * @param string $url
     * @param string $host
     * @param string $apiKey
     * @param string $endpoint
     * @return array
     */
    public function submitUrl(string $url, string $host, string $apiKey, string $endpoint = 'bing'): array
    {
        if (!$this->config['enabled']) {
            return [
                'success' => false,
                'error' => 'IndexNow is disabled',
            ];
        }

        $endpointUrl = $this->config['endpoints'][$endpoint] ?? $this->config['endpoints']['bing'];

        try {
            $response = Http::timeout(10)->post($endpointUrl, [
                'host' => $host,
                'key' => $apiKey,
                'urlList' => [$url],
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'url' => $url,
                    'endpoint' => $endpoint,
                    'status' => $response->status(),
                ];
            }

            return [
                'success' => false,
                'url' => $url,
                'endpoint' => $endpoint,
                'error' => 'HTTP ' . $response->status(),
                'response' => $response->body(),
            ];
        } catch (\Exception $e) {
            Log::error('IndexNow API Error', [
                'url' => $url,
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'url' => $url,
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Submit multiple URLs.
     *
     * @param array $urls
     * @param string $host
     * @param string $apiKey
     * @param string $endpoint
     * @return array
     */
    public function submitBulk(array $urls, string $host, string $apiKey, string $endpoint = 'bing'): array
    {
        if (!$this->config['enabled']) {
            return [
                'success' => false,
                'error' => 'IndexNow is disabled',
            ];
        }

        $endpointUrl = $this->config['endpoints'][$endpoint] ?? $this->config['endpoints']['bing'];

        try {
            $response = Http::timeout(30)->post($endpointUrl, [
                'host' => $host,
                'key' => $apiKey,
                'urlList' => $urls,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'count' => count($urls),
                    'endpoint' => $endpoint,
                    'status' => $response->status(),
                ];
            }

            return [
                'success' => false,
                'count' => count($urls),
                'endpoint' => $endpoint,
                'error' => 'HTTP ' . $response->status(),
                'response' => $response->body(),
            ];
        } catch (\Exception $e) {
            Log::error('IndexNow Bulk API Error', [
                'count' => count($urls),
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'count' => count($urls),
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Submit to multiple search engines.
     *
     * @param array $urls
     * @param string $host
     * @param string $apiKey
     * @param array $endpoints
     * @return array
     */
    public function submitToMultiple(array $urls, string $host, string $apiKey, array $endpoints = ['bing', 'yandex']): array
    {
        $results = [];

        foreach ($endpoints as $endpoint) {
            if (isset($this->config['endpoints'][$endpoint])) {
                $results[$endpoint] = $this->submitBulk($urls, $host, $apiKey, $endpoint);
            }
        }

        return $results;
    }
}

