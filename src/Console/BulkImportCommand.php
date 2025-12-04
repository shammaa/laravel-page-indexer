<?php

namespace Shammaa\LaravelPageIndexer\Console;

use Illuminate\Console\Command;
use Shammaa\LaravelPageIndexer\Models\Page;
use Shammaa\LaravelPageIndexer\Services\IndexingManager;

class BulkImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'page-indexer:bulk-import 
                            {urls : Comma-separated URLs or path to file with URLs (one per line)}
                            {--queue : Queue URLs for indexing instead of immediate processing}
                            {--chunk=100 : Process URLs in chunks}
                            {--method=both : Indexing method (google, indexnow, both)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bulk import URLs from a list or file for indexing';

    /**
     * Execute the console command.
     */
    public function handle(IndexingManager $manager): int
    {
        $urlsInput = $this->argument('urls');
        $shouldQueue = $this->option('queue');
        $chunkSize = (int) $this->option('chunk');
        $method = $this->option('method');

        // Parse URLs
        $urls = $this->parseUrls($urlsInput);
        
        if (empty($urls)) {
            $this->error('❌ No URLs found.');
            return Command::FAILURE;
        }

        $totalUrls = count($urls);
        $this->info("📄 Found {$totalUrls} URL(s)");
        $this->newLine();

        // Process in chunks
        $bar = $this->output->createProgressBar($totalUrls);
        $bar->start();

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $indexed = 0;

        foreach (array_chunk($urls, $chunkSize) as $chunk) {
            // First, create/update page records
            foreach ($chunk as $url) {
                $url = trim($url);
                
                if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                $page = Page::firstOrCreate(
                    [
                        'url' => $url,
                    ],
                    [
                        'indexing_status' => 'pending',
                        'indexing_method' => $method,
                    ]
                );

                if ($page->wasRecentlyCreated) {
                    $created++;
                } else {
                    // Update existing page
                    $page->update([
                        'indexing_method' => $method,
                        'indexing_status' => 'pending', // Reset to pending for re-indexing
                    ]);
                    $updated++;
                }

                $bar->advance();
            }

            // If not queuing, index immediately (in batches)
            if (!$shouldQueue && config('page-indexer.auto_indexing.enabled', false)) {
                $chunkUrls = array_filter($chunk, function($url) {
                    return !empty(trim($url)) && filter_var(trim($url), FILTER_VALIDATE_URL);
                });

                if (!empty($chunkUrls)) {
                    try {
                        $result = $manager->bulkIndex($chunkUrls, $method);
                        
                        // Count successful submissions
                        if (isset($result['google']) && $result['google']['success'] ?? false) {
                            $indexed += count($chunkUrls);
                        }
                    } catch (\Exception $e) {
                        $this->newLine();
                        $this->warn("  ⚠️  Error indexing batch: " . $e->getMessage());
                    }

                    // Rate limiting delay
                    sleep(2);
                }
            }
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->info('✨ Bulk import completed:');
        $this->table(
            ['Action', 'Count'],
            [
                ['✅ Created', $created],
                ['🔄 Updated', $updated],
                ['⏭️  Skipped', $skipped],
                ['📊 Total Processed', $totalUrls],
            ]
        );

        if ($shouldQueue) {
            $this->info('💡 URLs are queued. Run page-indexer:auto-index to process them.');
        } elseif ($indexed > 0) {
            $this->info("🚀 {$indexed} URL(s) submitted for indexing.");
        }

        return Command::SUCCESS;
    }

    /**
     * Parse URLs from input (file path or comma-separated string).
     */
    protected function parseUrls(string $input): array
    {
        // Check if it's a file path
        if (file_exists($input)) {
            $content = file_get_contents($input);
            return array_filter(array_map('trim', explode("\n", $content)));
        }

        // Check if it's comma-separated
        if (strpos($input, ',') !== false) {
            return array_filter(array_map('trim', explode(',', $input)));
        }

        // Single URL
        return [$input];
    }
}

