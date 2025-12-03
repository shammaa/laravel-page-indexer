<?php

namespace Shammaa\LaravelPageIndexer\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Shammaa\LaravelPageIndexer\Facades\PageIndexer;
use Shammaa\LaravelPageIndexer\Models\Page;
use Shammaa\LaravelPageIndexer\Models\Site;
use Shammaa\LaravelPageIndexer\Jobs\ProcessIndexingJob;
use Illuminate\Support\Facades\Queue;

class PageIndexingController extends Controller
{
    /**
     * Index a single URL.
     * 
     * POST /api/page-indexer/index
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|url',
            'site_id' => 'nullable|exists:page_indexer_sites,id',
            'method' => 'nullable|in:google,indexnow,both',
            'queue' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $url = $request->input('url');
        $siteId = $request->input('site_id');
        $method = $request->input('method', 'both');
        $queue = $request->input('queue', false);

        // Find site
        $site = $siteId 
            ? Site::find($siteId)
            : Site::where('google_site_url', $this->extractSiteUrl($url))->first();

        if (!$site) {
            return response()->json([
                'success' => false,
                'error' => 'Site not found. Please provide site_id or ensure site is configured.',
            ], 404);
        }

        if ($queue) {
            // Queue for background processing
            $page = Page::firstOrCreate(
                [
                    'site_id' => $site->id,
                    'url' => $url,
                ],
                [
                    'indexing_status' => 'pending',
                    'indexing_method' => $method,
                ]
            );

            ProcessIndexingJob::dispatch($page, $method);

            return response()->json([
                'success' => true,
                'queued' => true,
                'message' => 'URL queued for indexing',
                'page_id' => $page->id,
            ]);
        }

        // Index immediately
        $result = PageIndexer::index($url, $site, $method);

        // Create or update page record
        if ($result['success']) {
            $page = Page::firstOrCreate(
                [
                    'site_id' => $site->id,
                    'url' => $url,
                ],
                [
                    'indexing_status' => 'submitted',
                    'indexing_method' => $method,
                ]
            );

            if (!$page->wasRecentlyCreated) {
                $page->markAsSubmitted();
            }

            return response()->json([
                'success' => true,
                'message' => 'URL submitted for indexing',
                'page_id' => $page->id,
                'status' => $page->indexing_status,
                'result' => $result,
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => $result['error'] ?? 'Unknown error occurred',
            'result' => $result,
        ], 500);
    }

    /**
     * Check indexing status for a URL.
     * 
     * GET /api/page-indexer/status
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function status(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|url',
            'site_id' => 'nullable|exists:page_indexer_sites,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $url = $request->input('url');
        $siteId = $request->input('site_id');

        // Find page record
        $page = Page::where('url', $url);
        if ($siteId) {
            $page->where('site_id', $siteId);
        }
        $page = $page->first();

        // Find site
        $site = $page 
            ? $page->site 
            : ($siteId 
                ? Site::find($siteId)
                : Site::where('google_site_url', $this->extractSiteUrl($url))->first());

        if (!$site) {
            return response()->json([
                'success' => false,
                'error' => 'Site not found.',
            ], 404);
        }

        // Get status from database
        $statusData = [
            'status' => $page ? $page->indexing_status : 'pending',
            'last_indexed_at' => $page?->last_indexed_at?->toIso8601String(),
            'is_indexed' => $page ? $page->isIndexed() : false,
            'is_pending' => $page ? $page->isPending() : false,
            'has_failed' => $page ? $page->hasFailed() : false,
        ];

        // Optionally check via Google Search Console (more accurate but slower)
        if ($request->input('check_google', false)) {
            $result = PageIndexer::checkStatus($url, $site);
            
            if ($result['success'] && isset($result['inspectionResult'])) {
                $inspectionResult = $result['inspectionResult']['indexStatusResult'] ?? null;
                if ($inspectionResult) {
                    $statusData['google_status'] = [
                        'verdict' => $inspectionResult->getVerdict(),
                        'coverage_state' => $inspectionResult->getCoverageState(),
                        'indexing_state' => $inspectionResult->getIndexingState(),
                        'last_crawl_time' => $inspectionResult->getLastCrawlTime(),
                    ];
                    $statusData['is_indexed'] = ($inspectionResult->getCoverageState() === 'INDEXED');
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => $statusData,
        ]);
    }

    /**
     * Bulk index multiple URLs.
     * 
     * POST /api/page-indexer/bulk-index
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkIndex(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'urls' => 'required|array|min:1',
            'urls.*' => 'required|url',
            'site_id' => 'nullable|exists:page_indexer_sites,id',
            'method' => 'nullable|in:google,indexnow,both',
            'queue' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $urls = $request->input('urls');
        $siteId = $request->input('site_id');
        $method = $request->input('method', 'both');
        $queue = $request->input('queue', true);

        // Find site (use first URL to determine site if not provided)
        $site = $siteId 
            ? Site::find($siteId)
            : Site::where('google_site_url', $this->extractSiteUrl($urls[0]))->first();

        if (!$site) {
            return response()->json([
                'success' => false,
                'error' => 'Site not found.',
            ], 404);
        }

        if ($queue) {
            // Queue all URLs
            $queued = [];
            foreach ($urls as $url) {
                $page = Page::firstOrCreate(
                    [
                        'site_id' => $site->id,
                        'url' => $url,
                    ],
                    [
                        'indexing_status' => 'pending',
                        'indexing_method' => $method,
                    ]
                );

                ProcessIndexingJob::dispatch($page, $method);
                $queued[] = $page->id;
            }

            return response()->json([
                'success' => true,
                'queued' => true,
                'message' => count($urls) . ' URL(s) queued for indexing',
                'page_ids' => $queued,
            ]);
        }

        // Index immediately
        $result = PageIndexer::bulkIndex($urls, $site, $method);

        return response()->json([
            'success' => $result['success'] ?? true,
            'message' => 'Bulk indexing completed',
            'result' => $result,
        ]);
    }

    /**
     * Extract site URL from full URL.
     * 
     * @param string $url
     * @return string
     */
    protected function extractSiteUrl(string $url): string
    {
        $scheme = parse_url($url, PHP_URL_SCHEME);
        $host = parse_url($url, PHP_URL_HOST);
        return ($scheme ?? 'https') . '://' . $host . '/';
    }
}

