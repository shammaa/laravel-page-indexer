<?php

namespace Shammaa\LaravelPageIndexer\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    protected $table = 'page_indexer_pages';
    
    protected $fillable = [
        'site_id',
        'url',
        'indexing_status',
        'last_indexed_at',
        'indexing_method',
        'metadata',
    ];

    protected $casts = [
        'last_indexed_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the site that owns this page.
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Get all indexing jobs for this page.
     */
    public function indexingJobs(): HasMany
    {
        return $this->hasMany(IndexingJob::class);
    }

    /**
     * Get status history for this page.
     */
    public function statusHistory(): HasMany
    {
        return $this->hasMany(IndexingStatusHistory::class);
    }

    /**
     * Mark page as submitted.
     */
    public function markAsSubmitted(): void
    {
        $this->update([
            'indexing_status' => 'submitted',
            'last_indexed_at' => now(),
        ]);

        $this->statusHistory()->create([
            'status' => 'submitted',
            'checked_at' => now(),
        ]);
    }

    /**
     * Mark page as indexed.
     */
    public function markAsIndexed(string $searchEngine = null): void
    {
        $this->update([
            'indexing_status' => 'indexed',
            'last_indexed_at' => now(),
        ]);

        $this->statusHistory()->create([
            'status' => 'indexed',
            'search_engine' => $searchEngine,
            'checked_at' => now(),
        ]);
    }

    /**
     * Mark page as failed.
     */
    public function markAsFailed(string $error = null): void
    {
        $this->update([
            'indexing_status' => 'failed',
            'last_indexed_at' => now(),
            'metadata' => array_merge($this->metadata ?? [], ['last_error' => $error]),
        ]);

        $this->statusHistory()->create([
            'status' => 'failed',
            'checked_at' => now(),
            'metadata' => ['error' => $error],
        ]);
    }

    /**
     * Check if page is indexed.
     */
    public function isIndexed(): bool
    {
        return $this->indexing_status === 'indexed';
    }

    /**
     * Check if page is pending indexing.
     */
    public function isPending(): bool
    {
        return $this->indexing_status === 'pending';
    }

    /**
     * Check if page indexing failed.
     */
    public function hasFailed(): bool
    {
        return $this->indexing_status === 'failed';
    }

    /**
     * Scope: Get indexed pages.
     */
    public function scopeIndexed($query)
    {
        return $query->where('indexing_status', 'indexed');
    }

    /**
     * Scope: Get pending pages.
     */
    public function scopePending($query)
    {
        return $query->where('indexing_status', 'pending');
    }

    /**
     * Scope: Get failed pages.
     */
    public function scopeFailed($query)
    {
        return $query->where('indexing_status', 'failed');
    }
}

