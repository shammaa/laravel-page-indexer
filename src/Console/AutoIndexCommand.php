<?php

namespace Shammaa\LaravelPageIndexer\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Shammaa\LaravelPageIndexer\Jobs\ProcessIndexingJob;
use Shammaa\LaravelPageIndexer\Models\Page;

class AutoIndexCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'page-indexer:auto-index 
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
        // Check if auto-indexing is enabled
        if (!Config::get('page-indexer.auto_indexing.enabled', false)) {
            $this->warn('⚠️  Auto-indexing is disabled. Set AUTO_INDEXING_ENABLED=true in your .env file.');
            return Command::FAILURE;
        }

        $this->info('⚡ Starting auto-indexing...');
        $this->newLine();

        $limit = (int) $this->option('limit');
        $method = $this->option('method');

        // Get pending pages
        $pages = Page::where('indexing_status', 'pending')
            ->limit($limit)
            ->get();

        if ($pages->isEmpty()) {
            $this->warn('⚠️  No pending pages found.');
            return Command::SUCCESS;
        }

        $this->line("  📄 Found " . $pages->count() . " pending page(s)");

        // Check if IndexNow is configured
        $hasIndexNow = !empty(Config::get('page-indexer.site.indexnow_api_key', ''));

        $totalQueued = 0;

        // Queue indexing jobs
        foreach ($pages as $page) {
            // Determine which search engines to use
            $engines = [];
            
            if ($method === 'google' || $method === 'both') {
                $engines[] = 'google';
            }

            if ($method === 'indexnow' || $method === 'both') {
                if ($hasIndexNow) {
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

        $this->line("  ✅ Queued " . $totalQueued . " job(s) for indexing");
        $this->newLine();

        $this->info("✨ Auto-indexing completed: {$totalQueued} job(s) queued");

        return Command::SUCCESS;
    }
}

