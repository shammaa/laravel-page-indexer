<?php

namespace Shammaa\LaravelPageIndexer\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Shammaa\LaravelPageIndexer\Services\IndexingManager;

class SyncSitesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'page-indexer:sync-sites';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List sites from Google Search Console (for reference only)';

    /**
     * Execute the console command.
     */
    public function handle(IndexingManager $manager): int
    {
        $this->info('🔄 Fetching sites from Google Search Console...');
        $this->newLine();

        $result = $manager->syncSites();

        if (!$result['success']) {
            $this->error('❌ Failed to fetch sites: ' . ($result['error'] ?? 'Unknown error'));
            return Command::FAILURE;
        }

        $sites = $result['sites'] ?? [];
        
        if (empty($sites)) {
            $this->warn('⚠️  No sites found in Google Search Console.');
            return Command::SUCCESS;
        }

        $this->info('✅ Found ' . count($sites) . ' site(s) in Google Search Console:');
        $this->newLine();

        $tableData = [];
        foreach ($sites as $siteData) {
            $siteUrl = $siteData['siteUrl'];
            $permission = $siteData['permissionLevel'] ?? 'unknown';
            $isConfigured = (Config::get('page-indexer.site.google_site_url') === $siteUrl) ? '✅' : '❌';
            
            $tableData[] = [
                $isConfigured,
                $siteUrl,
                $permission,
            ];
        }

        $this->table(['Configured', 'Site URL', 'Permission'], $tableData);
        $this->newLine();
        
        $configuredUrl = Config::get('page-indexer.site.google_site_url');
        if (empty($configuredUrl)) {
            $this->warn('💡 Tip: Set GOOGLE_SITE_URL in your .env file to configure your site.');
        } else {
            $this->info("💡 Current configured site: {$configuredUrl}");
        }

        return Command::SUCCESS;
    }
}

