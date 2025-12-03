<?php

namespace Shammaa\LaravelPageIndexer\Console;

use Illuminate\Console\Command;
use Shammaa\LaravelPageIndexer\Models\Page;
use Shammaa\LaravelPageIndexer\Models\Site;
use Shammaa\LaravelPageIndexer\Models\Sitemap;
use Shammaa\LaravelPageIndexer\Services\IndexingManager;

class MonitorSitemapsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'page-indexer:monitor-sitemaps 
                            {--site-id= : Monitor specific site only}
                            {--force : Force re-check all sitemaps}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor sitemaps and discover new pages';

    /**
     * Execute the console command.
     */
    public function handle(IndexingManager $manager): int
    {
        $this->info('📝 Monitoring sitemaps...');
        $this->newLine();

        $siteId = $this->option('site-id');
        $sites = $siteId 
            ? Site::where('id', $siteId)->get()
            : Site::all();

        if ($sites->isEmpty()) {
            $this->warn('⚠️  No sites found. Run page-indexer:sync-sites first.');
            return Command::FAILURE;
        }

        $totalPages = 0;
        $newPages = 0;

        foreach ($sites as $site) {
            $this->info("🌐 Processing site: {$site->name} ({$site->google_site_url})");

            // Sync sitemaps
            $result = $manager->syncSitemaps($site);
            
            if (!$result['success']) {
                $this->error("  ❌ Failed to sync sitemaps: " . ($result['error'] ?? 'Unknown error'));
                continue;
            }

            $sitemaps = $result['sitemaps'] ?? [];
            $this->line("  📋 Found " . count($sitemaps) . " sitemap(s)");

            // Process each sitemap
            foreach ($sitemaps as $sitemapData) {
                $sitemapUrl = $sitemapData['path'];
                
                // Get or create sitemap record
                $sitemap = Sitemap::firstOrCreate(
                    [
                        'site_id' => $site->id,
                        'sitemap_url' => $sitemapUrl,
                    ],
                    [
                        'type' => $sitemapData['isSitemapsIndex'] ? 'sitemapindex' : 'sitemap',
                    ]
                );

                // Skip if recently checked (unless --force)
                if (!$this->option('force') && $sitemap->last_checked_at && $sitemap->last_checked_at->gt(now()->subHours(24))) {
                    $this->line("  ⏭️  Skipped: {$sitemapUrl} (checked recently)");
                    continue;
                }

                // Parse sitemap
                $parseResult = $manager->parseSitemap($sitemapUrl);
                
                if (!$parseResult['success']) {
                    $this->error("  ❌ Failed to parse: {$sitemapUrl}");
                    continue;
                }

                $urls = $parseResult['urls'] ?? [];
                $totalPages += count($urls);

                $this->line("  📄 Found " . count($urls) . " URL(s) in {$sitemapUrl}");

                // Create page records for new URLs
                foreach ($urls as $urlData) {
                    $url = $urlData['loc'];
                    
                    $page = Page::firstOrCreate(
                        [
                            'site_id' => $site->id,
                            'url' => $url,
                        ],
                        [
                            'indexing_status' => 'pending',
                            'indexing_method' => 'both',
                            'metadata' => [
                                'lastmod' => $urlData['lastmod'] ?? null,
                                'changefreq' => $urlData['changefreq'] ?? null,
                                'priority' => $urlData['priority'] ?? null,
                            ],
                        ]
                    );

                    if ($page->wasRecentlyCreated) {
                        $newPages++;
                    }
                }

                // Update sitemap
                $sitemap->markAsChecked(count($urls));
            }

            $this->newLine();
        }

        $this->info("✨ Monitoring completed: {$totalPages} total pages, {$newPages} new pages");
        
        return Command::SUCCESS;
    }
}

