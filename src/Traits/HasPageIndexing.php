<?php

namespace Shammaa\LaravelPageIndexer\Traits;

use Shammaa\LaravelPageIndexer\Facades\PageIndexer;
use Shammaa\LaravelPageIndexer\Models\Page;
use Illuminate\Support\Facades\Queue;

trait HasPageIndexing
{
    /**
     * Get the Page model associated with this model.
     * 
     * @return Page|null
     */
    public function getIndexedPageAttribute()
    {
        $url = $this->getIndexableUrl();
        
        return Page::where('url', $url)->first();
    }

    /**
     * Index this model's URL.
     * 
     * @param string $method
     * @param bool $queue
     * @return array
     */
    public function indexUrl(string $method = 'both', bool $queue = false): array
    {
        $url = $this->getIndexableUrl();

        if ($queue) {
            return $this->queueIndexing($url, $method);
        }

        $result = PageIndexer::index($url, $method);

        // Create or update page record
        $this->syncPageRecord($result);

        return $result;
    }

    /**
     * Check indexing status for this model's URL.
     * 
     * @return array
     */
    public function checkIndexingStatus(): array
    {
        $url = $this->getIndexableUrl();

        return PageIndexer::checkStatus($url);
    }

    /**
     * Get indexing status badge HTML.
     * 
     * @return string
     */
    public function getIndexingStatusBadge(): string
    {
        $page = $this->indexed_page;
        
        if (!$page) {
            return '<span class="badge badge-secondary">Not Submitted</span>';
        }

        return match($page->indexing_status) {
            'indexed' => '<span class="badge badge-success">✅ Indexed</span>',
            'submitted' => '<span class="badge badge-info">⏳ Submitted</span>',
            'pending' => '<span class="badge badge-warning">⏳ Pending</span>',
            'failed' => '<span class="badge badge-danger">❌ Failed</span>',
            default => '<span class="badge badge-secondary">Unknown</span>',
        };
    }

    /**
     * Check if this model's URL is indexed.
     * 
     * @return bool
     */
    public function isIndexed(): bool
    {
        $page = $this->indexed_page;
        return $page && $page->isIndexed();
    }

    /**
     * Get the URL to index for this model.
     * Override this method in your model if the URL is not in a standard field.
     * 
     * @return string
     */
    public function getIndexableUrl(): string
    {
        // Try common URL field names
        if (isset($this->url)) {
            return $this->url;
        }

        if (isset($this->slug)) {
            return route($this->getRouteName(), $this->slug);
        }

        // Fallback: generate URL from route
        if (method_exists($this, 'getRouteKeyName')) {
            return route($this->getRouteName(), $this->getRouteKey());
        }

        throw new \Exception('Cannot determine indexable URL. Override getIndexableUrl() method in your model.');
    }

    /**
     * Get the route name for this model.
     * Override this method in your model.
     * 
     * @return string
     */
    protected function getRouteName(): string
    {
        // Default route name based on model name
        $modelName = strtolower(class_basename($this));
        return "{$modelName}.show";
    }

    /**
     * Queue indexing job.
     * 
     * @param string $url
     * @param string $method
     * @return array
     */
    protected function queueIndexing(string $url, string $method): array
    {
        // Create page record first
        $page = $this->syncPageRecord(['success' => true]);

        // Dispatch job
        \Shammaa\LaravelPageIndexer\Jobs\ProcessIndexingJob::dispatch($page, $method);

        return [
            'success' => true,
            'queued' => true,
            'page_id' => $page->id,
        ];
    }

    /**
     * Sync page record in database.
     * 
     * @param array $result
     * @return Page
     */
    protected function syncPageRecord(array $result): Page
    {
        $url = $this->getIndexableUrl();

        $page = Page::firstOrCreate(
            [
                'url' => $url,
            ],
            [
                'indexing_status' => $result['success'] ? 'submitted' : 'pending',
                'indexing_method' => 'both',
            ]
        );

        // Update status based on result
        if ($result['success']) {
            $page->markAsSubmitted();
        } else {
            $page->markAsFailed($result['error'] ?? 'Unknown error');
        }

        return $page;
    }
}

