<?php

namespace Shammaa\LaravelPageIndexer\Models;

use Illuminate\Database\Eloquent\Model;

class Sitemap extends Model
{
    protected $table = 'page_indexer_sitemaps';
    
    protected $fillable = [
        'sitemap_url',
        'type',
        'last_checked_at',
        'page_count',
    ];

    protected $casts = [
        'last_checked_at' => 'datetime',
    ];

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

