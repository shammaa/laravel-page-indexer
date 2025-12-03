<?php

namespace Shammaa\LaravelPageIndexer\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sitemap extends Model
{
    protected $table = 'page_indexer_sitemaps';
    
    protected $fillable = [
        'site_id',
        'sitemap_url',
        'type',
        'last_checked_at',
        'page_count',
    ];

    protected $casts = [
        'last_checked_at' => 'datetime',
    ];

    /**
     * Get the site that owns this sitemap.
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Mark sitemap as checked.
     */
    public function markAsChecked(int $pageCount = 0): void
    {
        $this->update([
            'last_checked_at' => now(),
            'page_count' => $pageCount,
        ]);
    }
}

