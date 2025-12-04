<?php

namespace Shammaa\LaravelPageIndexer\Console;

use Illuminate\Console\Command;
use Shammaa\LaravelPageIndexer\Models\Page;
use Shammaa\LaravelPageIndexer\Services\IndexingManager;

class CheckStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'page-indexer:check-status 
                            {url? : Specific URL to check}
                            {--limit=100 : Maximum number of pages to check}
                            {--all : Check all pages (ignore limit)}
                            {--batch=10 : Number of pages to check per batch}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check indexing status for URLs via Google Search Console';

    /**
     * Execute the console command.
     */
    public function handle(IndexingManager $manager): int
    {
        $url = $this->argument('url');
        $limit = $this->option('all') ? null : (int) $this->option('limit');
        $batchSize = (int) $this->option('batch');

        // Check single URL
        if ($url) {
            return $this->checkSingleUrl($url, $manager);
        }

        // Check multiple pages
        $this->info('🔍 Checking indexing status...');
        $this->newLine();

        $query = Page::query();

        // Prioritize pages that haven't been checked recently
        $query->orderBy('last_indexed_at', 'asc')
              ->orderBy('id', 'asc');

        if ($limit) {
            $query->limit($limit);
        }

        $pages = $query->get();
        $totalPages = $pages->count();

        if ($totalPages === 0) {
            $this->warn('⚠️  No pages found to check.');
            return Command::FAILURE;
        }

        $this->info("📄 Found {$totalPages} page(s) to check");
        $this->line("🔄 Processing in batches of {$batchSize}...");
        $this->newLine();

        $bar = $this->output->createProgressBar($totalPages);
        $bar->start();

        $indexed = 0;
        $notIndexed = 0;
        $errors = 0;
        $skipped = 0;

        foreach ($pages->chunk($batchSize) as $chunk) {
            foreach ($chunk as $page) {
                try {
                    // Check status via Search Console
                    $result = $manager->checkStatus($page->url);

                    if (!$result['success']) {
                        $errors++;
                        $bar->advance();
                        continue;
                    }

                    $inspectionResult = $result['inspectionResult']['indexStatusResult'] ?? null;

                    if (!$inspectionResult) {
                        $skipped++;
                        $bar->advance();
                        continue;
                    }

                    // Determine status
                    $verdict = $inspectionResult->getVerdict() ?? 'unknown';
                    $coverageState = $inspectionResult->getCoverageState() ?? 'unknown';

                    // Update page status based on Google's response
                    if ($verdict === 'PASS' || $coverageState === 'INDEXED') {
                        $page->markAsIndexed('google');
                        $indexed++;
                    } elseif ($verdict === 'FAIL' || $coverageState === 'NOT_INDEXED') {
                        $page->update([
                            'indexing_status' => 'pending',
                        ]);
                        $notIndexed++;
                    }

                    // Small delay to avoid rate limiting
                    usleep(500000); // 0.5 seconds

                } catch (\Exception $e) {
                    $errors++;
                    $this->newLine();
                    $this->error("  ❌ Error checking {$page->url}: " . $e->getMessage());
                }

                $bar->advance();
            }

            // Longer delay between batches
            if ($chunk->count() === $batchSize) {
                sleep(1); // 1 second between batches
            }
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->info('✨ Status check completed:');
        $this->table(
            ['Status', 'Count'],
            [
                ['✅ Indexed', $indexed],
                ['⏳ Not Indexed', $notIndexed],
                ['❌ Errors', $errors],
                ['⏭️  Skipped', $skipped],
                ['📊 Total', $totalPages],
            ]
        );

        return Command::SUCCESS;
    }

    /**
     * Check status for a single URL.
     */
    protected function checkSingleUrl(string $url, IndexingManager $manager): int
    {
        $this->info("🔍 Checking status for: {$url}");
        $this->newLine();

        // Find the page
        $page = Page::where('url', $url)->first();

        // Check status
        $result = $manager->checkStatus($url);

        if (!$result['success']) {
            $this->error('❌ Failed to check status: ' . ($result['error'] ?? 'Unknown error'));
            return Command::FAILURE;
        }

        $inspectionResult = $result['inspectionResult']['indexStatusResult'] ?? null;

        if (!$inspectionResult) {
            $this->warn('⚠️  No inspection result available.');
            return Command::SUCCESS;
        }

        // Display detailed information
        $verdict = $inspectionResult->getVerdict() ?? 'unknown';
        $coverageState = $inspectionResult->getCoverageState() ?? 'unknown';
        $lastCrawlTime = $inspectionResult->getLastCrawlTime() ?? null;
        $indexingState = $inspectionResult->getIndexingState() ?? 'unknown';

        $this->table(
            ['Property', 'Value'],
            [
                ['URL', $url],
                ['Verdict', $verdict],
                ['Coverage State', $coverageState],
                ['Indexing State', $indexingState],
                ['Last Crawl Time', $lastCrawlTime ? date('Y-m-d H:i:s', strtotime($lastCrawlTime)) : 'N/A'],
            ]
        );

        // Status interpretation
        $this->newLine();
        if ($coverageState === 'INDEXED' || $verdict === 'PASS') {
            $this->info('✅ This URL is indexed in Google.');
        } elseif ($coverageState === 'NOT_INDEXED' || $verdict === 'FAIL') {
            $this->warn('⚠️  This URL is NOT indexed in Google.');
        } else {
            $this->comment('ℹ️  Status is uncertain. Check the values above.');
        }

        // Update page if exists
        if (isset($page)) {
            if ($coverageState === 'INDEXED') {
                $page->markAsIndexed('google');
                $this->line('✅ Page status updated in database.');
            }
        }

        return Command::SUCCESS;
    }
}

