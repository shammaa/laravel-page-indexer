<?php

namespace Shammaa\LaravelPageIndexer;

use Shammaa\LaravelPageIndexer\Services\GoogleIndexingService;
use Shammaa\LaravelPageIndexer\Services\IndexingManager;
use Shammaa\LaravelPageIndexer\Services\IndexNowService;
use Shammaa\LaravelPageIndexer\Services\SearchConsoleService;
use Shammaa\LaravelPageIndexer\Services\SitemapService;
use Illuminate\Support\ServiceProvider;

class LaravelPageIndexerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/page-indexer.php',
            'page-indexer'
        );

        // Register Services as singletons
        $this->app->singleton(GoogleIndexingService::class, function ($app) {
            return new GoogleIndexingService(config('page-indexer.google'));
        });

        $this->app->singleton(SearchConsoleService::class, function ($app) {
            return new SearchConsoleService(config('page-indexer.google'));
        });

        $this->app->singleton(IndexNowService::class, function ($app) {
            // Allow IndexNowService to work without config (for direct usage)
            $indexNowConfig = config('page-indexer.indexnow', []);
            return new IndexNowService($indexNowConfig);
        });

        $this->app->singleton(SitemapService::class);

        $this->app->singleton(IndexingManager::class, function ($app) {
            return new IndexingManager(
                $app->make(GoogleIndexingService::class),
                $app->make(SearchConsoleService::class),
                $app->make(IndexNowService::class),
                $app->make(SitemapService::class)
            );
        });

        $this->app->alias(IndexingManager::class, 'page-indexer');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__ . '/../config/page-indexer.php' => config_path('page-indexer.php'),
        ], 'page-indexer-config');

        // Publish migrations
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'page-indexer-migrations');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\SyncSitesCommand::class,
                Console\MonitorSitemapsCommand::class,
                Console\AutoIndexCommand::class,
                Console\CheckStatusCommand::class,
                Console\BulkImportCommand::class,
            ]);
        }
    }
}

