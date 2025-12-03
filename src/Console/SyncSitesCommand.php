<?php

namespace Shammaa\LaravelPageIndexer\Console;

use Illuminate\Console\Command;
use Shammaa\LaravelPageIndexer\Models\Site;
use Shammaa\LaravelPageIndexer\Services\IndexingManager;

class SyncSitesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'page-indexer:sync-sites 
                            {--token= : Google OAuth access token}
                            {--force : Force sync even if site exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync sites from Google Search Console';

    /**
     * Execute the console command.
     */
    public function handle(IndexingManager $manager): int
    {
        $this->info('🔄 Syncing sites from Google Search Console...');
        $this->newLine();

        $token = $this->option('token');
        $result = $manager->syncSites($token);

        if (!$result['success']) {
            $this->error('❌ Failed to sync sites: ' . ($result['error'] ?? 'Unknown error'));
            return Command::FAILURE;
        }

        $sites = $result['sites'] ?? [];
        
        if (empty($sites)) {
            $this->warn('⚠️  No sites found in Google Search Console.');
            return Command::SUCCESS;
        }

        $this->info('✅ Found ' . count($sites) . ' site(s):');
        $this->newLine();

        $created = 0;
        $updated = 0;

        foreach ($sites as $siteData) {
            $siteUrl = $siteData['siteUrl'];
            $permission = $siteData['permissionLevel'] ?? 'unknown';

            $site = Site::where('google_site_url', $siteUrl)->first();

            if ($site) {
                if ($this->option('force')) {
                    $site->update([
                        'name' => $this->extractDomain($siteUrl),
                    ]);
                    $updated++;
                    $this->line("  🔄 Updated: {$siteUrl}");
                } else {
                    $this->line("  ⏭️  Skipped (exists): {$siteUrl}");
                }
            } else {
                Site::create([
                    'google_site_url' => $siteUrl,
                    'name' => $this->extractDomain($siteUrl),
                    'auto_indexing_enabled' => false,
                ]);
                $created++;
                $this->line("  ✅ Created: {$siteUrl} ({$permission})");
            }
        }

        $this->newLine();
        $this->info("✨ Sync completed: {$created} created, {$updated} updated");

        return Command::SUCCESS;
    }

    /**
     * Extract domain name from URL.
     */
    protected function extractDomain(string $url): string
    {
        $parsed = parse_url($url);
        return $parsed['host'] ?? $url;
    }
}

