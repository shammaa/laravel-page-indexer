<?php

namespace Shammaa\LaravelPageIndexer\Traits;

use Shammaa\LaravelPageIndexer\Facades\PageIndexer;
use Shammaa\LaravelPageIndexer\Models\Page;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Log;

trait HasPageIndexing
{
    /**
     * Whether to automatically index when model is created/updated.
     * Set to false in your model to disable auto-indexing.
     * 
     * @var bool
     */
    protected $autoIndexOnCreate = true;
    
    /**
     * Whether to automatically index when model is updated.
     * Set to false in your model to disable auto-indexing on update.
     * 
     * @var bool
     */
    protected $autoIndexOnUpdate = true;
    
    /**
     * Indexing method to use for auto-indexing.
     * Options: 'google', 'indexnow', 'both'
     * 
     * @var string
     */
    protected $autoIndexMethod = 'both';
    
    /**
     * Whether to queue auto-indexing jobs.
     * Set to true to queue indexing instead of immediate processing.
     * 
     * @var bool
     */
    protected $autoIndexQueue = false;

    /**
     * Boot the trait and register model events.
     */
    public static function bootHasPageIndexing()
    {
        // Auto-index on create
        static::created(function ($model) {
            if ($model->shouldAutoIndexOnCreate() && $model->isPublished()) {
                $model->autoIndex();
            }
        });

        // Auto-index on update (if published)
        static::updated(function ($model) {
            if ($model->shouldAutoIndexOnUpdate() && $model->isPublished()) {
                // Check if URL changed or status changed to published
                if ($model->wasChanged('url') || $model->wasChanged('status') || $model->wasChanged('published_at')) {
                    $model->autoIndex();
                }
            }
        });
    }

    /**
     * Check if auto-indexing should run on create.
     * 
     * @return bool
     */
    protected function shouldAutoIndexOnCreate(): bool
    {
        return $this->autoIndexOnCreate ?? true;
    }

    /**
     * Check if auto-indexing should run on update.
     * 
     * @return bool
     */
    protected function shouldAutoIndexOnUpdate(): bool
    {
        return $this->autoIndexOnUpdate ?? true;
    }

    /**
     * Check if the model is published and should be indexed.
     * Override this method in your model to customize the logic.
     * 
     * @return bool
     */
    public function isPublished(): bool
    {
        // Check for common published status fields
        if (isset($this->status)) {
            return $this->status === 'published' || $this->status === 'active';
        }

        if (isset($this->is_published)) {
            return (bool) $this->is_published;
        }

        if (isset($this->published_at)) {
            return $this->published_at !== null && $this->published_at->isPast();
        }

        // If no published field exists, assume it's published
        return true;
    }

    /**
     * Automatically index this model.
     * Called automatically on create/update if enabled.
     * 
     * @return array|null
     */
    public function autoIndex(): ?array
    {
        try {
            return $this->indexUrl($this->autoIndexMethod ?? 'both', $this->autoIndexQueue ?? false);
        } catch (\Exception $e) {
            Log::error('Auto-indexing failed', [
                'model' => get_class($this),
                'id' => $this->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

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

