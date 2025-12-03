<?php

namespace Shammaa\LaravelPageIndexer\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    protected $table = 'page_indexer_sites';
    
    protected $fillable = [
        'google_site_url',
        'name',
        'auto_indexing_enabled',
        'indexnow_api_key',
        'settings',
    ];

    protected $casts = [
        'auto_indexing_enabled' => 'boolean',
        'settings' => 'array',
    ];


    /**
     * Get all pages for this site.
     */
    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }

    /**
     * Get all sitemaps for this site.
     */
    public function sitemaps(): HasMany
    {
        return $this->hasMany(Sitemap::class);
    }


    /**
     * Check if IndexNow is configured.
     */
    public function hasIndexNowKey(): bool
    {
        return !empty($this->indexnow_api_key);
    }
}

