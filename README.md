# Laravel Page Indexer

[![Latest Version](https://img.shields.io/badge/latest-v1.2.0-blue.svg)](https://github.com/shammaa/laravel-page-indexer)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Laravel](https://img.shields.io/badge/laravel-9.0%2B-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/php-8.1%2B-purple.svg)](https://php.net)

**Professional automated page indexing tool for Laravel** - Automatically submit and monitor your website pages to Google, Bing, Yandex, and other search engines. Get your pages indexed within 24-48 hours instead of waiting weeks or months.

---

## 📋 Table of Contents

- [Overview](#-overview)
- [Key Features](#-key-features)
- [Two Usage Modes](#-two-usage-modes)
- [Requirements](#-requirements)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Usage Guide](#-usage-guide)
- [Artisan Commands](#-artisan-commands)
- [API Reference](#-api-reference)
- [Database Structure](#-database-structure)
- [Best Practices](#-best-practices)
- [Troubleshooting](#-troubleshooting)
- [Contributing](#-contributing)
- [License](#-license)

---

## 🎯 Overview

**Laravel Page Indexer** is a comprehensive solution for automating the entire page indexing workflow. Instead of manually submitting URLs or waiting for search engines to discover your content naturally, this package handles everything automatically.

### What It Does

- ✅ **Submits pages to Google** via Google Indexing API (fastest method - 24-48 hours)
- ✅ **Submits to multiple search engines** (Bing, Yandex, Naver) via IndexNow API
- ✅ **Monitors sitemaps** automatically and discovers new pages
- ✅ **Tracks indexing status** with complete history and timeline
- ✅ **Runs completely automatically** - set it up once and forget it

### The Problem It Solves

**Without Laravel Page Indexer:**
- ❌ Pages take weeks or months to get indexed naturally
- ❌ Manual submission is tedious and time-consuming
- ❌ No way to track indexing status or history
- ❌ Missing organic traffic due to delayed indexing

**With Laravel Page Indexer:**
- ✅ **Pages indexed in 24-48 hours** automatically
- ✅ **Zero manual work** - fully automated workflow
- ✅ **Complete status tracking** with timeline history
- ✅ **Increased organic traffic** from faster indexing

> **📌 Important:** This package is designed to work with **one website per installation**. The "site" refers to **your website** (e.g., `https://example.com/`), not Google Search Console sites. Site configuration is stored in your `.env` file, not in the database. If you need to manage multiple websites, you'll need separate installations for each.

---

## ✨ Key Features

### 🔍 Google Indexing API Integration
Submit pages directly to Google using their official Indexing API. This is the fastest way to get your pages indexed by Google - typically within 24-48 hours.

### 📊 Google Search Console Integration
Seamlessly sync your sitemaps directly from Google Search Console. Configure your site URL once and the package handles the rest.

### 🚀 IndexNow API Support
Submit pages to multiple search engines at once: Bing, Yandex, Naver, DuckDuckGo, and more using the open IndexNow protocol.

### 📝 Automatic Sitemap Monitoring
The package automatically monitors your XML sitemaps, discovers new pages, and queues them for indexing - completely hands-free.

### ⚡ Fully Automated Indexing
Enable auto-indexing and the package will:
- Monitor sitemaps daily
- Discover new pages automatically
- Submit them to search engines
- Track indexing status
- Store complete history

### 📈 Complete Status Tracking
Track the indexing status of every page with a complete timeline history. Know exactly when pages were submitted, indexed, or if there were any errors.

### 🔄 Bulk Operations
Index multiple pages at once with built-in queue support. Perfect for large sites with hundreds or thousands of pages.

### 🎯 Queue Support
Background processing ensures your application stays responsive. All indexing jobs run in the background via Laravel queues.

---

## 🎯 Two Usage Modes

This package offers **two distinct ways** to use it, depending on your needs:

### Mode 1: Direct Service Usage (Simple - No Database) ✅

**Perfect for:** Simple projects that just need to submit URLs without tracking.

**What you need:**
- ✅ Only `GOOGLE_SERVICE_ACCOUNT_PATH` in `.env` (for Google)
- ❌ No migrations needed
- ❌ No `GOOGLE_SITE_URL` needed
- ❌ No `INDEXNOW_API_KEY` in `.env` (pass it directly as parameter)

**Use when:**
- You just need to submit URLs quickly
- You don't need tracking or history
- You want minimal setup
- You're building a simple integration

### Mode 2: Full Page Indexer (With Database Tracking) 📊

**Perfect for:** Projects that need complete tracking, history, and statistics.

**What you need:**
- ✅ `GOOGLE_SERVICE_ACCOUNT_PATH` in `.env`
- ✅ `GOOGLE_SITE_URL` in `.env`
- ✅ Run migrations (`php artisan migrate`)
- ✅ `INDEXNOW_API_KEY` in `.env` (optional, but recommended)

**Use when:**
- You need complete status tracking
- You want indexing history and statistics
- You need automatic sitemap monitoring
- You're building a comprehensive SEO solution

---

## 📋 Requirements

- **PHP:** 8.1 or higher
- **Laravel:** 9.0 or higher
- **Google Cloud Project** with Indexing API enabled
- **Google Search Console** account
- **Composer**

---

## 🔑 API Setup & Prerequisites

Before installing the package, you need to set up the required APIs:

### 1. Google Indexing API Setup

**Why:** To submit pages directly to Google (fastest indexing method).

#### Step 1: Create Google Cloud Project
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing one
3. **Link:** https://console.cloud.google.com/projectcreate

#### Step 2: Enable Indexing API
1. Navigate to **APIs & Services** > **Library**
2. Search for "Indexing API"
3. Click **Enable**
4. **Link:** https://console.cloud.google.com/apis/library/indexing.googleapis.com

#### Step 3: Create Service Account
1. Go to **APIs & Services** > **Credentials**
2. Click **Create Credentials** > **Service Account**
3. Fill in details and create
4. **Link:** https://console.cloud.google.com/apis/credentials

#### Step 4: Download Service Account Key
1. Click on the created service account
2. Go to **Keys** tab
3. Click **Add Key** > **Create new key**
4. Choose **JSON** format
5. Download and save securely

#### Step 5: Grant Owner Permission in Search Console
1. Go to [Google Search Console](https://search.google.com/search-console)
2. Select your property (website)
3. Go to **Settings** > **Users and permissions**
4. Click **Add User**
5. Add the service account email (found in JSON file)
6. Grant **Owner** permissions
7. **Link:** https://search.google.com/search-console

**Environment Variable:**
```env
GOOGLE_SERVICE_ACCOUNT_PATH=/absolute/path/to/service-account.json
```

**Documentation:**
- [Google Indexing API Guide](https://developers.google.com/search/apis/indexing-api/v3/using-api)
- [Service Account Setup](https://developers.google.com/identity/protocols/oauth2/service-account)

---

### 2. Google Search Console API Setup

**Why:** To automatically sync your sitemaps and check indexing status.

#### Enable Search Console API
1. In your Google Cloud Project (same project where you created the Service Account)
2. Go to **APIs & Services** > **Library**
3. Search for "Google Search Console API"
4. Click **Enable**
5. **Link:** https://console.cloud.google.com/apis/library/webmasters.googleapis.com

**Note:** The same Service Account used for Indexing API will work for Search Console API. Just make sure it's added as Owner in Search Console.

**Documentation:**
- [Search Console API Guide](https://developers.google.com/webmaster-tools/search-console-api-original/v1)

---

### 3. IndexNow API Key Setup (Optional - Only for Mode 2)

> **Note:** If you're using **Mode 1 (Direct Service Usage)**, you don't need to set `INDEXNOW_API_KEY` in `.env`. You can pass it directly as a parameter when calling the service.

**Why:** To submit pages to Bing, Yandex, Naver, and other search engines (only needed for Mode 2 - Full Page Indexer).

#### Step 1: Generate API Key
Create a random 32-character string:
- Example: `a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6`
- Or use: `php artisan page-indexer:generate-indexnow-key` (if available)

#### Step 2: Create Verification File
1. Create file: `{your-api-key}.txt`
2. Place it in your website root directory (e.g., `public/`)
3. File content: Just the API key (same value)
4. Example: `public/a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6.txt`

#### Step 3: Verify File is Accessible
Test: `https://yoursite.com/a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6.txt`
- Should return the API key

**Environment Variables (Only for Mode 2):**
```env
INDEXNOW_API_KEY=a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6
```

> **Note:** `INDEXNOW_ENABLED` is optional and defaults to `true`. You can set it to `false` in config if you want to disable IndexNow for Mode 2.

**For Mode 1 (Direct Service Usage):** You don't need any IndexNow variables in `.env`. Just pass the API key directly as a parameter:
```php
submit_to_indexnow($url, $host, 'your-api-key-here', 'bing');
```

**Documentation:**
- [IndexNow Protocol](https://www.indexnow.org/)
- [IndexNow Implementation Guide](https://www.indexnow.org/implementation-guide)

**Supported Search Engines:**
- Bing: https://www.bing.com/indexnow
- Yandex: https://yandex.com/indexnow
- Naver: https://searchadvisor.naver.com/indexnow
- Seznam: https://search.seznam.cz/indexnow

---

## 📦 Installation

### Step 1: Install Package

```bash
composer require shammaa/laravel-page-indexer
```

### Step 2: Publish Configuration & Migrations

```bash
# Publish configuration file
php artisan vendor:publish --tag=page-indexer-config

# Publish migration files (only needed for Mode 2)
php artisan vendor:publish --tag=page-indexer-migrations
```

**What this does:**
- Creates `config/page-indexer.php` in your config directory
- Copies migration files to `database/migrations/` directory

### Step 3: Run Migrations (Mode 2 Only)

If you're using **Mode 2 (Full Page Indexer)**, run migrations:

```bash
php artisan migrate
```

**What this creates:**
- `page_indexer_pages` - Stores all pages to be indexed
- `page_indexer_jobs` - Tracks indexing job queue
- `page_indexer_sitemaps` - Stores sitemap information
- `page_indexer_status_history` - Complete history of indexing status changes

> **Note:** If you're using **Mode 1**, you can skip this step.

### Step 4: Configure Environment Variables

Add to your `.env` file:

```env
# Google API Configuration (Required for both modes)
# Use absolute path to your service account JSON file
GOOGLE_SERVICE_ACCOUNT_PATH=/absolute/path/to/service-account.json

# Example for Windows:
# GOOGLE_SERVICE_ACCOUNT_PATH=E:\laravel\project\storage\app\google-service-account.json

# Example for Linux/Mac:
# GOOGLE_SERVICE_ACCOUNT_PATH=/var/www/html/storage/app/google-service-account.json

# Site Configuration (Required for Mode 2 only)
# Your website URL as registered in Google Search Console
# Note: Not needed for Mode 1 (Direct Service Usage)
GOOGLE_SITE_URL=https://example.com/

# IndexNow Configuration (Optional - Only for Mode 2)
# Note: For Mode 1, pass API key directly as parameter (no .env needed)
INDEXNOW_API_KEY=your-32-character-key

# Auto-Indexing (Optional - Only for Mode 2)
AUTO_INDEXING_ENABLED=true
AUTO_INDEXING_SCHEDULE=daily
```

**Important Notes:**
- Use **absolute path** (full path) for `GOOGLE_SERVICE_ACCOUNT_PATH`
- Make sure the JSON file is readable by your web server
- Keep the JSON file secure and never commit it to version control

### Step 5: Test the Connection

Verify that everything is set up correctly:

```bash
# Check if commands are available
php artisan list | grep page-indexer

# Test connection by listing sites from Google Search Console (Mode 2)
php artisan page-indexer:sync-sites
```

**Expected Output:**
- List of all available page-indexer commands
- List of sites from Google Search Console (for reference)
- Shows which site is configured in your `.env` file

**If you see errors:**
- Check Service Account JSON file path is correct
- Verify Service Account has Owner permissions in Search Console
- Make sure Google APIs are enabled (Indexing API & Search Console API)

---

## ✅ Installation Checklist

Use this checklist to verify your installation:

- [ ] Package installed via Composer
- [ ] Configuration file published (`config/page-indexer.php` exists)
- [ ] Migration files published (check `database/migrations/` directory) - **Mode 2 only**
- [ ] Migrations run successfully (`php artisan migrate`) - **Mode 2 only**
- [ ] Google Service Account JSON file downloaded
- [ ] Service Account email added to Google Search Console as Owner
- [ ] `GOOGLE_SERVICE_ACCOUNT_PATH` set in `.env` (absolute path)
- [ ] `GOOGLE_SITE_URL` set in `.env` (your website URL) - **Only for Mode 2**
- [ ] IndexNow API key generated (optional) - **Only for Mode 2**
- [ ] `INDEXNOW_API_KEY` set in `.env` (optional) - **Only for Mode 2**
- [ ] Connection tested successfully (`php artisan page-indexer:sync-sites`) - **Mode 2 only**

**All checked?** You're ready to use the library! 🎉

---

## 💻 Usage Guide

### Mode 1: Direct Service Usage (No Database)

Use the services directly **without** database, migrations, or extra configuration.

#### Using GoogleIndexingService

```php
use Shammaa\LaravelPageIndexer\Facades\GoogleIndexing;

// Submit single URL to Google
$result = GoogleIndexing::submitUrl('https://example.com/page');

// Submit multiple URLs to Google
$results = GoogleIndexing::submitBulk([
    'https://example.com/page1',
    'https://example.com/page2',
]);

// Or using helper function
$result = submit_to_google('https://example.com/page');
```

**Response:**
```php
[
    'success' => true,
    'url' => 'https://example.com/page',
    'notification' => [...]
]
```

#### Using IndexNowService

```php
use Shammaa\LaravelPageIndexer\Facades\IndexNow;

// Submit to Bing
$result = IndexNow::submitUrl(
    'https://example.com/page',
    'https://example.com',  // host (domain)
    'your-api-key-here',    // IndexNow API key (pass directly)
    'bing'                  // endpoint: 'bing', 'yandex', 'naver'
);

// Submit to multiple search engines at once
$results = IndexNow::submitToMultiple(
    ['https://example.com/page1', 'https://example.com/page2'],
    'https://example.com',
    'your-api-key-here',
    ['bing', 'yandex']  // Submit to both Bing and Yandex
);

// Or using helper function
$result = submit_to_indexnow(
    'https://example.com/page',
    'https://example.com',
    'your-api-key-here',
    'bing'
);
```

**Response:**
```php
[
    'success' => true,
    'url' => 'https://example.com/page',
    'endpoint' => 'bing',
    'status' => 202
]
```

#### Complete Example (Mode 1)

```php
// Submit to Google
$googleResult = submit_to_google('https://example.com/page');

// Submit to IndexNow (Bing, Yandex, etc.)
$indexNowResult = submit_to_indexnow(
    'https://example.com/page',
    'https://example.com',
    'your-indexnow-api-key',
    'bing'
);

if ($googleResult['success'] && $indexNowResult['success']) {
    echo "✅ Submitted to both Google and Bing!";
}
```

---

### Mode 2: Full Page Indexer (With Database Tracking)

Use the complete package with database tracking, status monitoring, and statistics.

#### Manual Indexing

**Index a Single URL:**

```php
use Shammaa\LaravelPageIndexer\Facades\PageIndexer;

// Index to both Google and IndexNow
$results = PageIndexer::index('https://example.com/page', 'both');

// Index to Google only
$results = PageIndexer::index('https://example.com/page', 'google');

// Index to IndexNow only (Bing, Yandex, etc.)
$results = PageIndexer::index('https://example.com/page', 'indexnow');
```

**Bulk Indexing:**

```php
$urls = [
    'https://example.com/page1',
    'https://example.com/page2',
    'https://example.com/page3',
];

$results = PageIndexer::bulkIndex($urls, 'both');
```

#### Automatic Indexing

The package offers **two levels** of automatic indexing:

**Level 1: Immediate (via Trait) - No Scheduling Needed!**
- ✅ When you create/update a post with `HasPageIndexing` trait, it **automatically** sends to Google immediately
- ✅ No scheduling needed - works instantly when you save the model
- ✅ Perfect for real-time indexing as you publish content

**Level 2: Scheduled (via Commands) - For Bulk & Discovery**
- ✅ **Monitors sitemaps** - Checks for new pages daily (discovers existing articles too!)
- ✅ **Discovers new URLs** - Compares with database
- ✅ **Queues for indexing** - Creates background jobs
- ✅ **Submits to search engines** - Google and IndexNow
- ✅ **Tracks status** - Updates database with results
- ✅ **Checks status** - Verifies indexing status from Google

> **💡 Best Practice:** Use both! Trait for immediate indexing of new posts, and scheduling for discovering existing articles and checking status.

**Enable Auto-Indexing:**

```env
AUTO_INDEXING_ENABLED=true
```

**Schedule Commands:**

#### Laravel 11: Use `bootstrap/app.php`

```php
// bootstrap/app.php

return Application::configure(basePath: dirname(__DIR__))
    // ... other configuration
    ->withSchedule(function ($schedule) {
        // Send new articles every 10 minutes
        $schedule->command('page-indexer:auto-index --limit=50')
            ->everyTenMinutes()
            ->withoutOverlapping();
        
        // Check indexing status every 10 minutes
        $schedule->command('page-indexer:check-status --limit=50 --batch=10')
            ->everyTenMinutes()
            ->withoutOverlapping();
        
        // Monitor sitemaps daily
        $schedule->command('page-indexer:monitor-sitemaps')->daily();
    })
    ->create();
```

#### Laravel 10 or below: Use `app/Console/Kernel.php`

```php
// app/Console/Kernel.php

protected function schedule(Schedule $schedule)
{
    // Send new articles every 10 minutes
    $schedule->command('page-indexer:auto-index --limit=50')
        ->everyTenMinutes()
        ->withoutOverlapping();
    
    // Check indexing status every 10 minutes
    $schedule->command('page-indexer:check-status --limit=50 --batch=10')
        ->everyTenMinutes()
        ->withoutOverlapping();
    
    // Monitor sitemaps daily
    $schedule->command('page-indexer:monitor-sitemaps')->daily();
}
```

**What This Does:**
1. **Every 10 minutes:** Sends pending articles to Google and saves them to database
2. **Every 10 minutes:** Checks status of submitted articles and updates database
3. **Daily:** Monitors sitemaps for new URLs (discovers existing articles too!)

**Result:** Fully automatic indexing with status updates stored in database!

**Important:** You still need ONE Cron Job to run `php artisan schedule:run` every minute.

> **📖 For detailed scheduling guide:** See [SCHEDULING_GUIDE.md](SCHEDULING_GUIDE.md) for:
> - Complete scheduling examples
> - How to discover existing articles
> - Advanced scheduling options
> - Cron Job setup instructions
> - Best practices

#### Check Indexing Status

**Check if a URL is Indexed:**

```php
use Shammaa\LaravelPageIndexer\Models\Page;

$page = Page::where('url', 'https://example.com/page')->first();

// Quick check
if ($page->isIndexed()) {
    echo "✅ Indexed!";
    echo "Indexed at: " . $page->last_indexed_at;
}

// Get current status
$status = $page->indexing_status; // pending, submitted, indexed, failed

// Get status history (complete timeline)
$history = $page->statusHistory()->orderBy('checked_at', 'desc')->get();
foreach ($history as $entry) {
    echo $entry->checked_at . ": " . $entry->status;
}

// Check via Google Search Console (most accurate)
$result = PageIndexer::checkStatus('https://example.com/page');
if ($result['success']) {
    $coverageState = $result['inspectionResult']['indexStatusResult']->getCoverageState();
    echo "Status: " . $coverageState; // INDEXED or NOT_INDEXED
}

// Using helper function
if (is_url_indexed('https://example.com/page')) {
    echo "✅ Indexed!";
}
```

**Working with Models:**

```php
use Shammaa\LaravelPageIndexer\Models\Page;

// Get all pages
$pages = Page::all();

// Get pending pages
$pendingPages = Page::where('indexing_status', 'pending')->get();

// Get indexed pages
$indexedPages = Page::where('indexing_status', 'indexed')->get();

// Get page with history
$page = Page::with('statusHistory')->find(1);

// Use scopes for easier queries
$indexed = Page::indexed()->count();
$pending = Page::pending()->count();
$failed = Page::failed()->count();

// Check status
if ($page->isIndexed()) {
    echo "Indexed at: " . $page->last_indexed_at;
}
```

#### Using with Eloquent Models (Automatic Indexing)

> **✨ Automatic Indexing:** When you add `HasPageIndexing` trait to your model, it **automatically** sends articles to Google and saves them to database **immediately** when you create or update them. No manual code needed in your Controller!

You can add indexing functionality to your existing models:

```php
use Shammaa\LaravelPageIndexer\Traits\HasPageIndexing;

class Post extends Model
{
    use HasPageIndexing;  // ✅ This is all you need! Works automatically!
    
    protected $fillable = ['title', 'slug', 'content', 'status', 'published_at'];
    
    // Optional: Customize auto-indexing behavior
    protected $autoIndexOnCreate = true;  // Auto-index when created (default: true)
    protected $autoIndexOnUpdate = true;  // Auto-index when updated (default: true)
    protected $autoIndexMethod = 'both';  // 'google', 'indexnow', or 'both'
    protected $autoIndexQueue = false;    // Set to true to queue instead of immediate
    
    // Optional: Override URL generation
    public function getIndexableUrl(): string
    {
        return route('posts.show', $this->slug);
    }
    
    // Optional: Override published check (if you have custom logic)
    public function isPublished(): bool
    {
        return $this->status === 'published' && $this->published_at !== null;
    }
}

// ============================================
// Automatic Indexing (No Controller Code Needed!)
// ============================================
// When you create or update a post, it AUTOMATICALLY:
// 1. ✅ Sends to Google for indexing
// 2. ✅ Adds to database (page_indexer_pages table)
// 3. ✅ Tracks status and history

// In your Controller - Just create the post normally:
$post = Post::create([
    'title' => 'My New Article',
    'slug' => 'my-new-article',
    'content' => '...',
    'status' => 'published',
    'published_at' => now(),
]);
// ✅ Automatically indexed and added to database!
// ✅ No additional code needed in Controller!

// ============================================
// Manual Indexing (if needed):
// ============================================
$post = Post::find(1);

// Index the post URL manually
$post->indexUrl();

// Check if indexed
if ($post->isIndexed()) {
    echo "Post is indexed!";
}

// Get indexed page record
$indexedPage = $post->indexed_page;
echo "Status: " . $indexedPage->indexing_status;

// Queue for indexing (background job)
$post->indexUrl('both', true);

// Check indexing status via Google
$result = $post->checkIndexingStatus();
```

**How It Works:**
- The trait listens to `created` and `updated` events automatically
- When a post is created/updated and is published, it automatically calls `autoIndex()`
- `autoIndex()` sends to Google and saves to database - all automatically!
- No need to add any code in your Controller - it just works! ✨

---

## 🛠️ Artisan Commands

### List Sites from Google Search Console

```bash
php artisan page-indexer:sync-sites
```

This command lists all sites from Google Search Console and shows which one is configured in your `.env` file.

### Monitor Sitemaps

```bash
php artisan page-indexer:monitor-sitemaps
```

**Options:**
- `--force`: Force re-check all sitemaps (ignores 24-hour cache)

This command monitors sitemaps for your configured site (set in `GOOGLE_SITE_URL`).

### Auto-Index Pending Pages

```bash
php artisan page-indexer:auto-index
```

**Options:**
- `--limit=100`: Maximum pages to index (default: 100)
- `--method=both`: Indexing method (google, indexnow, both)

**Note:** Requires `AUTO_INDEXING_ENABLED=true` in your `.env` file.

### Check Indexing Status

```bash
# Check a specific URL
php artisan page-indexer:check-status "https://example.com/page"

# Check multiple pages
php artisan page-indexer:check-status --limit=100

# Check all pages
php artisan page-indexer:check-status --all

# Check with batches (recommended for large numbers)
php artisan page-indexer:check-status --limit=1000 --batch=10
```

**Options:**
- `url`: Specific URL to check (optional)
- `--limit=`: Maximum number of pages to check (default: 100)
- `--all`: Check all pages (ignores limit)
- `--batch=`: Number of pages to check per batch (default: 10)

### Bulk Import URLs

Import large numbers of URLs from a file or comma-separated list:

```bash
# From file (one URL per line)
php artisan page-indexer:bulk-import urls.txt

# From comma-separated string
php artisan page-indexer:bulk-import "url1,url2,url3"

# Queue for later indexing
php artisan page-indexer:bulk-import urls.txt --queue
```

**Options:**
- `urls`: Comma-separated URLs or path to file with URLs (one per line)
- `--queue`: Queue URLs for indexing instead of immediate processing
- `--chunk=`: Process URLs in chunks (default: 100)
- `--method=`: Indexing method (google, indexnow, both) - default: both

---

## ⚙️ Configuration

After publishing the config file, you can customize settings in `config/page-indexer.php`:

### Site Configuration

```php
'site' => [
    'google_site_url' => env('GOOGLE_SITE_URL', ''),
    'indexnow_api_key' => env('INDEXNOW_API_KEY', ''),
],
```

**Important:** 
- `google_site_url` should match the URL format you registered in Google Search Console
- This is **your website URL** (e.g., `https://example.com/`), not a Google site
- The package works with **one website** per installation

### Google API Configuration

```php
'google' => [
    'service_account_path' => env('GOOGLE_SERVICE_ACCOUNT_PATH'),
    'scopes' => [
        'https://www.googleapis.com/auth/indexing',
        'https://www.googleapis.com/auth/webmasters.readonly',
    ],
],
```

### IndexNow Configuration

```php
'indexnow' => [
    'enabled' => env('INDEXNOW_ENABLED', true),
    'api_key_length' => env('INDEXNOW_API_KEY_LENGTH', 32),
    'endpoints' => [
        'bing' => 'https://api.indexnow.org/IndexNow',
        'yandex' => 'https://yandex.com/indexnow',
        'naver' => 'https://searchadvisor.naver.com/indexnow',
    ],
],
```

### Auto-Indexing Configuration

```php
'auto_indexing' => [
    'enabled' => env('AUTO_INDEXING_ENABLED', false),
    'schedule' => env('AUTO_INDEXING_SCHEDULE', 'daily'),
    'check_new_pages_interval' => env('CHECK_NEW_PAGES_INTERVAL', 24), // hours
    'max_pages_per_batch' => env('MAX_PAGES_PER_BATCH', 100),
],
```

### Rate Limiting

```php
'rate_limiting' => [
    'google' => [
        'max_per_day' => env('GOOGLE_MAX_INDEXING_PER_DAY', 200),
        'max_per_minute' => env('GOOGLE_MAX_INDEXING_PER_MINUTE', 10),
    ],
    'indexnow' => [
        'max_per_day' => env('INDEXNOW_MAX_PER_DAY', 10000),
        'max_per_minute' => env('INDEXNOW_MAX_PER_MINUTE', 100),
    ],
],
```

### Queue Configuration

```php
'queue' => [
    'connection' => env('PAGE_INDEXER_QUEUE_CONNECTION', 'default'),
    'queue' => env('PAGE_INDEXER_QUEUE', 'default'),
],
```

### Cache Configuration

```php
'cache' => [
    'enabled' => env('PAGE_INDEXER_CACHE_ENABLED', true),
    'ttl' => env('PAGE_INDEXER_CACHE_TTL', 3600), // 1 hour
],
```

---

## 📚 API Reference

### Facades

#### PageIndexer Facade (Mode 2)

```php
use Shammaa\LaravelPageIndexer\Facades\PageIndexer;

// Index a single URL
PageIndexer::index(string $url, string $method = 'both'): array

// Index multiple URLs
PageIndexer::bulkIndex(array $urls, string $method = 'both'): array

// Check indexing status
PageIndexer::checkStatus(string $url): array

// List sites from Search Console (for reference)
PageIndexer::syncSites(): array

// Sync sitemaps for configured site
PageIndexer::syncSitemaps(): array

// Parse sitemap XML
PageIndexer::parseSitemap(string $sitemapUrl): array
```

#### GoogleIndexing Facade (Mode 1)

```php
use Shammaa\LaravelPageIndexer\Facades\GoogleIndexing;

// Submit single URL
GoogleIndexing::submitUrl(string $url, string $type = 'URL_UPDATED'): array

// Submit multiple URLs
GoogleIndexing::submitBulk(array $urls, string $type = 'URL_UPDATED'): array
```

#### IndexNow Facade (Mode 1)

```php
use Shammaa\LaravelPageIndexer\Facades\IndexNow;

// Submit to single endpoint
IndexNow::submitUrl(string $url, string $host, string $apiKey, string $endpoint = 'bing', bool $enabled = true): array

// Submit to multiple endpoints
IndexNow::submitBulk(array $urls, string $host, string $apiKey, string $endpoint = 'bing', bool $enabled = true): array

// Submit to multiple search engines
IndexNow::submitToMultiple(array $urls, string $host, string $apiKey, array $endpoints = ['bing', 'yandex']): array
```

### Helper Functions

#### For Full Page Indexer (Mode 2)

```php
// Get PageIndexer instance
$indexer = page_indexer();

// Index a page
index_page(string $url, string $method = 'both'): array

// Bulk index
bulk_index(array $urls, string $method = 'both'): array

// Check indexing status
check_indexing_status(string $url): array

// Check if URL is indexed (returns boolean)
is_url_indexed(string $url): bool
```

#### For Direct Service Usage (Mode 1)

```php
// Get GoogleIndexingService instance
$googleService = google_indexing();

// Get IndexNowService instance
$indexNowService = indexnow();

// Submit to Google directly
submit_to_google(string $url, string $type = 'URL_UPDATED'): array

// Submit to IndexNow directly
submit_to_indexnow(string $url, string $host, string $apiKey, string $endpoint = 'bing'): array
```

---

## 📊 Database Structure

The package creates 4 tables (Mode 2 only):

1. **`page_indexer_pages`** - Stores all pages to be indexed
   - `id`, `url`, `indexing_status`, `last_indexed_at`, `created_at`, `updated_at`

2. **`page_indexer_jobs`** - Stores indexing job history
   - `id`, `page_id`, `job_id`, `status`, `method`, `created_at`, `updated_at`

3. **`page_indexer_sitemaps`** - Stores sitemap information
   - `id`, `sitemap_url`, `last_checked_at`, `url_count`, `created_at`, `updated_at`

4. **`page_indexer_status_history`** - Stores complete status timeline
   - `id`, `page_id`, `status`, `checked_at`, `created_at`

**Note:** Site configuration is stored in `config/page-indexer.php` and `.env` file, not in the database.

---

## 🔄 Queue Configuration

For large sites, it's recommended to use queues for background processing:

```php
// In config/page-indexer.php or .env
'queue' => [
    'connection' => env('PAGE_INDEXER_QUEUE_CONNECTION', 'default'),
    'queue' => env('PAGE_INDEXER_QUEUE', 'default'),
],
```

Make sure your queue worker is running:

```bash
php artisan queue:work
```

---

## 🎯 Best Practices

### 1. Use Queue for Bulk Operations
Always use queue for bulk operations to keep your application responsive:

```php
// Queue indexing instead of immediate processing
PageIndexer::bulkIndex($urls, 'both', true); // third parameter = queue
```

### 2. Respect Rate Limits
- Google: 200 URLs per day per site
- IndexNow: No official limit, but don't abuse
- Use batch processing for large numbers of URLs

### 3. Monitor Status Regularly
Check indexing status regularly to ensure pages are being indexed:

```bash
php artisan page-indexer:check-status --limit=100
```

### 4. Handle Errors Gracefully
Check job failures and retry if needed:

```bash
php artisan queue:failed
```

### 5. Use Auto-Indexing for Continuous Monitoring
Enable auto-indexing and schedule commands for hands-free operation:

```php
// In Kernel.php
$schedule->command('page-indexer:monitor-sitemaps')->daily();
$schedule->command('page-indexer:auto-index')->daily();
```

### 6. Keep Service Account Secure
- Never commit Service Account JSON file to version control
- Use absolute paths for `GOOGLE_SERVICE_ACCOUNT_PATH`
- Restrict file permissions appropriately

---

## ⚠️ Important Notes

### Google Indexing API Limits

- **200 URLs per day** per site (Google's limit)
- Requires **Owner** permissions in Search Console
- Only works for **Job Posting** or **Video** content types (unless site is verified)

**Workaround:** Use IndexNow for additional submissions (no daily limit).

### IndexNow

- **No official daily limit** (but don't abuse)
- Supports multiple search engines
- Requires API key verification file

### Content Types

Google Indexing API works best with:
- Job Postings
- Video content
- Verified properties in Search Console

For other content types, IndexNow is recommended.

---

## 🐛 Troubleshooting

### "Invalid Google token" Error

**Solution:**
1. Check Service Account JSON file path
2. Verify Service Account has Owner permissions in Search Console
3. Clear config cache: `php artisan config:clear`
4. Verify the JSON file is valid and readable

### "Sitemap not found" Error

**Solution:**
1. Ensure sitemap is submitted in Google Search Console
2. Check sitemap URL is accessible
3. Run: `php artisan page-indexer:monitor-sitemaps --force`
4. Verify `GOOGLE_SITE_URL` matches your Search Console property

### Pages Not Getting Indexed

**Check:**
1. Auto-indexing is enabled (`AUTO_INDEXING_ENABLED=true` in `.env`)
2. `GOOGLE_SITE_URL` is correctly set in `.env`
3. Queue worker is running (`php artisan queue:work`)
4. Check job failures: `php artisan queue:failed`
5. Check page status in database
6. Verify Service Account has Owner permissions

### "Service Account not found" Error

**Solution:**
1. Verify `GOOGLE_SERVICE_ACCOUNT_PATH` is set correctly (absolute path)
2. Check file permissions (must be readable)
3. Verify JSON file is valid
4. Clear config cache: `php artisan config:clear`

### IndexNow Verification Failed

**Solution:**
1. Verify API key file exists at `{api-key}.txt` in website root
2. Check file is accessible via HTTP
3. Verify file content matches API key exactly
4. Check file permissions

---

## 🎯 Real-World Examples

### Example 1: Blog Post Indexing

```php
// After creating a new blog post
use Shammaa\LaravelPageIndexer\Facades\PageIndexer;

$post = Post::create([...]);
$postUrl = route('posts.show', $post);

// Index immediately
PageIndexer::index($postUrl, 'both');
```

### Example 2: E-commerce Product Indexing

```php
// After creating/updating a product
$product = Product::create([...]);
$productUrl = route('products.show', $product);

// Index to both Google and IndexNow
$result = PageIndexer::index($productUrl, 'both');

if ($result['google']['success'] && $result['indexnow']['success']) {
    // Log success
    Log::info("Product indexed successfully: {$productUrl}");
}
```

### Example 3: Bulk Indexing After Migration

```php
// After migrating content from another platform
$urls = Product::pluck('slug')->map(function ($slug) {
    return route('products.show', $slug);
})->toArray();

// Queue for background processing
PageIndexer::bulkIndex($urls, 'both', true);
```

### Example 4: Using with Model Events

```php
use Shammaa\LaravelPageIndexer\Traits\HasPageIndexing;

class Article extends Model
{
    use HasPageIndexing;
    
    protected static function booted()
    {
        static::created(function ($article) {
            $article->queueIndexing();
        });
        
        static::updated(function ($article) {
            if ($article->wasChanged('published_at')) {
                $article->queueIndexing();
            }
        });
    }
}
```

---

## 📖 Additional Documentation

- [Using with DataTable](DATATABLE_USAGE.md) - Complete guide for integrating with DataTable
- [How It Works](HOW_IT_WORKS.md) - Detailed technical explanation
- [Getting Started](GETTING_STARTED.md) - Step-by-step setup guide
- [API Documentation](https://github.com/shammaa/laravel-page-indexer/wiki)

---

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

---

## 📄 License

This package is open-sourced software licensed under the [MIT license](LICENSE).

---

## 👤 Author

**Shadi Shammaa**

- Email: shadi.shammaa@gmail.com
- GitHub: [@shammaa](https://github.com/shammaa)

---

## 🙏 Acknowledgments

- Google for providing the Indexing API
- Microsoft for the IndexNow protocol
- All contributors and users of this package

---

## ⭐ Show Your Support

If you find this package useful, please give it a ⭐ on [GitHub](https://github.com/shammaa/laravel-page-indexer)!

---

**Made with ❤️ for the Laravel community**
