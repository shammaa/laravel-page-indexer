<?php

namespace Shammaa\LaravelPageIndexer\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Shammaa\LaravelPageIndexer\Facades\PageIndexer;
use Shammaa\LaravelPageIndexer\Models\Page;
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
        $method = $request->input('method', 'both');
        $queue = $request->input('queue', false);

        if ($queue) {
            // Queue for background processing
            $page = Page::firstOrCreate(
                [
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
        $result = PageIndexer::index($url, $method);

        // Create or update page record
        if ($result['success'] ?? false) {
            $page = Page::firstOrCreate(
                [
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
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $url = $request->input('url');

        // Find page record
        $page = Page::where('url', $url)->first();

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
            $result = PageIndexer::checkStatus($url);
            
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
        $method = $request->input('method', 'both');
        $queue = $request->input('queue', true);

        if ($queue) {
            // Queue all URLs
            $queued = [];
            foreach ($urls as $url) {
                $page = Page::firstOrCreate(
                    [
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
        $result = PageIndexer::bulkIndex($urls, $method);

        return response()->json([
            'success' => $result['success'] ?? true,
            'message' => 'Bulk indexing completed',
            'result' => $result,
        ]);
    }

}

