<?php

namespace Shammaa\LaravelPageIndexer\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Shammaa\LaravelPageIndexer\Models\IndexingJob;
use Shammaa\LaravelPageIndexer\Models\Page;
use Shammaa\LaravelPageIndexer\Services\IndexingManager;

class ProcessIndexingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Page $page,
        public string $searchEngine = 'google'
    ) {}

    /**
     * Execute the job.
     */
    public function handle(IndexingManager $manager): void
    {
        $job = IndexingJob::create([
            'page_id' => $this->page->id,
            'status' => 'processing',
            'search_engine' => $this->searchEngine,
            'request_data' => [
                'url' => $this->page->url,
                'method' => $this->page->indexing_method,
            ],
        ]);

        try {
            $job->markAsProcessing();

            $method = $this->searchEngine === 'google' ? 'google' : 'indexnow';
            $results = $manager->index($this->page->url, $method);

            if (isset($results[$this->searchEngine])) {
                $result = $results[$this->searchEngine];

                if ($result['success'] ?? false) {
                    $job->markAsCompleted($result);
                    
                    if ($this->searchEngine === 'google' || $this->searchEngine === 'indexnow') {
                        $this->page->markAsSubmitted();
                    }
                } else {
                    $job->markAsFailed($result['error'] ?? 'Unknown error', $result);
                    $this->page->markAsFailed($result['error'] ?? 'Unknown error');
                }
            } else {
                $error = 'No result from ' . $this->searchEngine;
                $job->markAsFailed($error);
                $this->page->markAsFailed($error);
            }
        } catch (\Exception $e) {
            $job->markAsFailed($e->getMessage());
            $this->page->markAsFailed($e->getMessage());
            
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $job = IndexingJob::where('page_id', $this->page->id)
            ->where('status', 'processing')
            ->where('search_engine', $this->searchEngine)
            ->latest()
            ->first();

        if ($job) {
            $job->markAsFailed($exception->getMessage());
        }

        $this->page->markAsFailed($exception->getMessage());
    }
}

