<?php

namespace Shammaa\LaravelPageIndexer\Console;

use Illuminate\Console\Command;
use Shammaa\LaravelPageIndexer\Jobs\ProcessIndexingJob;
use Shammaa\LaravelPageIndexer\Models\Page;
use Shammaa\LaravelPageIndexer\Models\Site;

class AutoIndexCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'page-indexer:auto-index 
                            {--site-id= : Index specific site only}
                            {--limit=100 : Maximum number of pages to index}
                            {--method=both : Indexing method (google, indexnow, both)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically index pending pages';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('⚡ Starting auto-indexing...');
        $this->newLine();

        $siteId = $this->option('site-id');
        $limit = (int) $this->option('limit');
        $method = $this->option('method');

        // Get sites with auto-indexing enabled
        $query = Site::where('auto_indexing_enabled', true);
        
        if ($siteId) {
            $query->where('id', $siteId);
        }

        $sites = $query->get();

        if ($sites->isEmpty()) {
            $this->warn('⚠️  No sites with auto-indexing enabled found.');
            return Command::FAILURE;
        }

        $totalQueued = 0;

        foreach ($sites as $site) {
            $this->info("🌐 Processing site: {$site->name}");

            // Get pending pages
            $pages = Page::where('site_id', $site->id)
                ->where('indexing_status', 'pending')
                ->limit($limit)
                ->get();

            if ($pages->isEmpty()) {
                $this->line("  ℹ️  No pending pages found");
                continue;
            }

            $this->line("  📄 Found " . $pages->count() . " pending page(s)");

            // Queue indexing jobs
            foreach ($pages as $page) {
                // Determine which search engines to use
                $engines = [];
                
                if ($method === 'google' || $method === 'both') {
                    $engines[] = 'google';
                }

                if ($method === 'indexnow' || $method === 'both') {
                    if ($site->hasIndexNowKey()) {
                        $engines[] = 'indexnow';
                    }
                }

                // Dispatch jobs
                foreach ($engines as $engine) {
                    ProcessIndexingJob::dispatch($page, $engine)
                        ->onQueue(config('page-indexer.queue.queue', 'default'));
                    
                    $totalQueued++;
                }
            }

            $this->line("  ✅ Queued " . count($pages) . " page(s) for indexing");
            $this->newLine();
        }

        $this->info("✨ Auto-indexing completed: {$totalQueued} job(s) queued");

        return Command::SUCCESS;
    }
}

