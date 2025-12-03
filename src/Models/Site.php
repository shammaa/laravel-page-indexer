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
        'google_access_token',
        'google_refresh_token',
        'google_token_expires_at',
        'indexnow_api_key',
        'settings',
    ];

    protected $casts = [
        'auto_indexing_enabled' => 'boolean',
        'google_token_expires_at' => 'datetime',
        'settings' => 'array',
    ];

    protected $hidden = [
        'google_access_token',
        'google_refresh_token',
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
     * Check if Google token is valid.
     */
    public function hasValidGoogleToken(): bool
    {
        if (!$this->google_access_token) {
            return false;
        }

        if ($this->google_token_expires_at && $this->google_token_expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Check if IndexNow is configured.
     */
    public function hasIndexNowKey(): bool
    {
        return !empty($this->indexnow_api_key);
    }
}

