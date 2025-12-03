<?php

namespace Shammaa\LaravelPageIndexer\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IndexingStatusHistory extends Model
{
    protected $table = 'page_indexer_status_history';
    
    protected $fillable = [
        'page_id',
        'status',
        'search_engine',
        'metadata',
        'checked_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'checked_at' => 'datetime',
    ];

    /**
     * Get the page that owns this history entry.
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}

