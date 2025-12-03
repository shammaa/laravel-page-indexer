<?php

namespace Shammaa\LaravelPageIndexer\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IndexingJob extends Model
{
    protected $table = 'page_indexer_jobs';
    
    protected $fillable = [
        'page_id',
        'status',
        'search_engine',
        'request_data',
        'response_data',
        'error_message',
        'processed_at',
    ];

    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
        'processed_at' => 'datetime',
    ];

    /**
     * Get the page that owns this job.
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Mark job as processing.
     */
    public function markAsProcessing(): void
    {
        $this->update(['status' => 'processing']);
    }

    /**
     * Mark job as completed.
     */
    public function markAsCompleted(array $responseData = []): void
    {
        $this->update([
            'status' => 'completed',
            'response_data' => $responseData,
            'processed_at' => now(),
        ]);
    }

    /**
     * Mark job as failed.
     */
    public function markAsFailed(string $error, array $responseData = []): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $error,
            'response_data' => $responseData,
            'processed_at' => now(),
        ]);
    }
}

