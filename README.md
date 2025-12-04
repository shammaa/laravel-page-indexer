# Laravel Page Indexer

[![Latest Version](https://img.shields.io/badge/latest-v1.2.0-blue.svg)](https://github.com/shammaa/laravel-page-indexer)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Laravel](https://img.shields.io/badge/laravel-9.0%2B-red.svg)](https://laravel.com)

**Professional automated page indexing tool for Laravel** - Submit and monitor pages to Google, Bing, Yandex, and other search engines automatically. Get your pages indexed within 24-48 hours instead of waiting weeks or months.

> **📌 Important:** This package is designed to work with **one website per installation**. The "site" refers to **your website** (e.g., `https://example.com/`), not Google Search Console sites. Site configuration is stored in your `.env` file, not in the database. If you need to manage multiple websites, you'll need separate installations for each.

---

## 🎯 What This Package Does

This package automates the entire process of getting your website pages indexed by search engines. Instead of manually submitting URLs or waiting for search engines to crawl your site naturally, **Laravel Page Indexer** does everything automatically:

- ✅ **Submits new pages to Google** via Google Indexing API (within 24-48 hours)
- ✅ **Submits to multiple search engines** (Bing, Yandex, Naver) via IndexNow API
- ✅ **Monitors your sitemaps** automatically and discovers new pages
- ✅ **Tracks indexing status** with complete history and timeline
- ✅ **Runs completely automatically** - set it up once and forget it

### 🚀 Key Benefits

**Before Laravel Page Indexer:**
- ❌ Pages take weeks or months to get indexed
- ❌ Manual submission is tedious and time-consuming
- ❌ No way to track indexing status
- ❌ Missing organic traffic due to delayed indexing

**With Laravel Page Indexer:**
- ✅ **Pages indexed in 24-48 hours** automatically
- ✅ **Zero manual work** - fully automated
- ✅ **Complete status tracking** with timeline history
- ✅ **Increased organic traffic** from faster indexing

---

## ✨ Features

### 🔍 Google Indexing API Integration
Automatically submit pages to Google using their official Indexing API. This is the fastest way to get your pages indexed by Google - typically within 24-48 hours.

### 📊 Google Search Console Integration
Seamlessly sync your sitemaps directly from Google Search Console. Configure your site URL once in config file.

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

## 📋 Requirements

- PHP 8.1 or higher
- Laravel 9.0 or higher
- Google Cloud Project with Indexing API enabled
- Google Search Console account
- Composer

---

## 🔑 Required API Keys & Setup

### 1. Google Indexing API

**Why:** To submit pages directly to Google (fastest indexing method).

**Setup Steps:**

1. **Create Google Cloud Project**
   - Go to [Google Cloud Console](https://console.cloud.google.com/)
   - Create a new project or select existing one
   - **Link:** https://console.cloud.google.com/projectcreate

2. **Enable Indexing API**
   - Navigate to **APIs & Services** > **Library**
   - Search for "Indexing API"
   - Click **Enable**
   - **Link:** https://console.cloud.google.com/apis/library/indexing.googleapis.com

3. **Create Service Account**
   - Go to **APIs & Services** > **Credentials**
   - Click **Create Credentials** > **Service Account**
   - Fill in details and create
   - **Link:** https://console.cloud.google.com/apis/credentials

4. **Download Service Account Key**
   - Click on the created service account
   - Go to **Keys** tab
   - Click **Add Key** > **Create new key**
   - Choose **JSON** format
   - Download and save securely

5. **Grant Owner Permission in Search Console**
   - Go to [Google Search Console](https://search.google.com/search-console)
   - Select your property (website)
   - Go to **Settings** > **Users and permissions**
   - Click **Add User**
   - Add the service account email (found in JSON file)
   - Grant **Owner** permissions
   - **Link:** https://search.google.com/search-console

**Environment Variables:**
```env
GOOGLE_SERVICE_ACCOUNT_PATH=/path/to/service-account.json
```
Simply download the JSON file from Google Cloud Console and point to it.

**Documentation:**
- [Google Indexing API Guide](https://developers.google.com/search/apis/indexing-api/v3/using-api)
- [Service Account Setup](https://developers.google.com/identity/protocols/oauth2/service-account)

---

### 2. Google Search Console API

**Why:** To automatically sync your sitemaps and check indexing status.

**Setup Steps:**

1. **Enable Search Console API**
   - In your Google Cloud Project (same project where you created the Service Account)
   - Go to **APIs & Services** > **Library**
   - Search for "Google Search Console API"
   - Click **Enable**
   - **Link:** https://console.cloud.google.com/apis/library/webmasters.googleapis.com

**Note:** The same Service Account used for Indexing API will work for Search Console API. Just make sure it's added as Owner in Search Console.

**Documentation:**
- [Search Console API Guide](https://developers.google.com/webmaster-tools/search-console-api-original/v1)

---

### 3. IndexNow API Key (Optional but Recommended)

**Why:** To submit pages to Bing, Yandex, Naver, and other search engines.

**Setup Steps:**

1. **Generate API Key**
   - Create a random 32-character string
   - Example: `a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6`
   - Or use: `php artisan page-indexer:generate-indexnow-key`

2. **Create Verification File**
   - Create file: `{your-api-key}.txt`
   - Place it in your website root directory
   - File content: Just the API key (same value)
   - Example: `public/a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6.txt`

3. **Verify File is Accessible**
   - Test: `https://yoursite.com/a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6.txt`
   - Should return the API key

**Environment Variables:**
```env
INDEXNOW_ENABLED=true
INDEXNOW_API_KEY=a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6
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

**Complete installation guide - follow these steps in order:**

1. Install package via Composer
2. Publish configuration and migrations
3. Run migrations to create database tables
4. Set up Google Service Account (see Prerequisites above)
5. Configure environment variables
6. Test the connection

---

### Step 1: Install Package

```bash
composer require shammaa/laravel-page-indexer
```

### Step 2: Publish Configuration & Migrations

```bash
# Publish configuration file
php artisan vendor:publish --tag=page-indexer-config

# Publish migration files
php artisan vendor:publish --tag=page-indexer-migrations
```

**What this does:**
- Creates `config/page-indexer.php` in your config directory
- Copies migration files to `database/migrations/` directory

### Step 3: Run Migrations

```bash
php artisan migrate
```

**What this creates:**
- `pages` table - Stores all pages to be indexed
- `indexing_jobs` table - Tracks indexing job queue
- `sitemaps` table - Stores sitemap information
- `indexing_status_history` table - Complete history of indexing status changes

### Step 4: Set Up Google Service Account

**Before proceeding, make sure you've completed the Prerequisites section above.**

1. **Download Service Account JSON file** from Google Cloud Console
2. **Place it in a secure location** (e.g., `storage/app/google-service-account.json`)
3. **Add Service Account email to Google Search Console** as Owner:
   - Go to [Google Search Console](https://search.google.com/search-console)
   - Select your property → Settings → Users and permissions
   - Add the service account email (found in the JSON file)
   - Grant **Owner** permissions

### Step 5: Configure Environment Variables

Add to your `.env` file:

```env
# Google API Configuration (Required)
# Use absolute path to your service account JSON file
GOOGLE_SERVICE_ACCOUNT_PATH=/absolute/path/to/service-account.json

# Example for Windows:
# GOOGLE_SERVICE_ACCOUNT_PATH=E:\laravel\project\storage\app\google-service-account.json

# Example for Linux/Mac:
# GOOGLE_SERVICE_ACCOUNT_PATH=/var/www/html/storage/app/google-service-account.json

# Site Configuration (Required)
# Your website URL as registered in Google Search Console
GOOGLE_SITE_URL=https://example.com/

# IndexNow Configuration (Optional but Recommended)
INDEXNOW_ENABLED=true
INDEXNOW_API_KEY=your-32-character-key

# Auto-Indexing (Optional)
AUTO_INDEXING_ENABLED=true
AUTO_INDEXING_SCHEDULE=daily
```

**Important Notes:**
- Use **absolute path** (full path) for `GOOGLE_SERVICE_ACCOUNT_PATH`
- Make sure the JSON file is readable by your web server
- Keep the JSON file secure and never commit it to version control

### Step 6: Test the Connection

Verify that everything is set up correctly:

```bash
# Check if commands are available
php artisan list | grep page-indexer

# Test connection by listing sites from Google Search Console
php artisan page-indexer:sync-sites
```

**Expected Output:**
- List of all available page-indexer commands
- List of sites from Google Search Console (for reference)
- Shows which site is configured in your `.env` file

If you see errors, check:
- Service Account JSON file path is correct
- Service Account has Owner permissions in Search Console
- Google APIs are enabled (Indexing API & Search Console API)

---

## ✅ Installation Checklist

Use this checklist to verify your installation:

- [ ] Package installed via Composer
- [ ] Configuration file published (`config/page-indexer.php` exists)
- [ ] Migration files published (check `database/migrations/` directory)
- [ ] Migrations run successfully (`php artisan migrate`)
- [ ] Google Service Account JSON file downloaded
- [ ] Service Account email added to Google Search Console as Owner
- [ ] `GOOGLE_SERVICE_ACCOUNT_PATH` set in `.env` (absolute path)
- [ ] `GOOGLE_SITE_URL` set in `.env` (your website URL)
- [ ] IndexNow API key generated (optional)
- [ ] `INDEXNOW_API_KEY` set in `.env` (optional)
- [ ] Connection tested successfully (`php artisan page-indexer:sync-sites`)

**All checked?** You're ready to use the library! 🎉

---

## 🚀 Quick Start

> **Note:** Make sure you've completed all Installation steps above before proceeding.

### Step 1: Configure Your Site URL

Make sure you've set `GOOGLE_SITE_URL` in your `.env` file:

```env
GOOGLE_SITE_URL=https://example.com/
```

**Important:** This should match the URL format you registered in Google Search Console (with or without trailing slash).

### Step 2: Verify Your Site Configuration

```bash
php artisan page-indexer:sync-sites
```

This command will:
- Connect to your Google Search Console
- List all your verified sites
- Show which site is currently configured in your `.env` file

**If this fails:**
- Check your `GOOGLE_SERVICE_ACCOUNT_PATH` in `.env`
- Verify Service Account has Owner permissions in Search Console
- Make sure Indexing API and Search Console API are enabled

### Step 3: Monitor Sitemaps and Discover Pages

```bash
php artisan page-indexer:monitor-sitemaps
```

This command will:
- Fetch sitemaps from Search Console for your configured site
- Parse all sitemap XML files
- Extract all URLs
- Create page records for new URLs

### Step 4: Enable Auto-Indexing

Enable auto-indexing in your `.env` file:

```env
AUTO_INDEXING_ENABLED=true
```

Or set it in `config/page-indexer.php`:

```php
'auto_indexing' => [
    'enabled' => true,
    // ...
],
```

### Step 5: Run Auto-Indexing

Add to your `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Run auto-indexing daily
    $schedule->command('page-indexer:auto-index')->daily();
    
    // Monitor sitemaps daily
    $schedule->command('page-indexer:monitor-sitemaps')->daily();
}
```

---

## 🎯 Usage Modes

This package offers **two ways** to use it:

### Mode 1: Direct Service Usage (Simple - No Database) ✅

Use the services directly **without** database, migrations, or extra configuration.

**What you need:**
- ✅ Only `GOOGLE_SERVICE_ACCOUNT_PATH` in `.env` (for Google)
- ❌ No migrations needed
- ❌ No `GOOGLE_SITE_URL` needed
- ❌ No `INDEXNOW_API_KEY` in `.env` (pass it directly as parameter)

**Perfect for:** Simple projects that just need to submit URLs without tracking.

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
submit_to_google('https://example.com/page');
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
submit_to_indexnow(
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

### Mode 2: Full Page Indexer (With Database Tracking) 📊

Use the complete package with database tracking, status monitoring, and statistics.

**What you need:**
- ✅ `GOOGLE_SERVICE_ACCOUNT_PATH` in `.env`
- ✅ `GOOGLE_SITE_URL` in `.env`
- ✅ Run migrations (`php artisan migrate`)
- ✅ `INDEXNOW_API_KEY` in `.env` (optional, but recommended)

**Perfect for:** Projects that need complete tracking, history, and statistics.

**Features:**
- 📊 Complete status tracking in database
- 📈 Statistics and history
- 🔄 Automatic sitemap monitoring
- ⚡ Queue support for background processing
- 📝 Full indexing history timeline

See the sections below for full usage.

---

## 💻 Usage

### Manual Indexing

#### Index a Single URL

```php
use Shammaa\LaravelPageIndexer\Facades\PageIndexer;

// Index to both Google and IndexNow
$results = PageIndexer::index('https://example.com/page', 'both');

// Index to Google only
$results = PageIndexer::index('https://example.com/page', 'google');

// Index to IndexNow only (Bing, Yandex, etc.)
$results = PageIndexer::index('https://example.com/page', 'indexnow');
```

#### Bulk Indexing

```php
$urls = [
    'https://example.com/page1',
    'https://example.com/page2',
    'https://example.com/page3',
];

$results = PageIndexer::bulkIndex($urls, 'both');
```

### Automatic Indexing

Once auto-indexing is enabled, the system automatically:

1. **Monitors sitemaps** - Checks for new pages daily
2. **Discovers new URLs** - Compares with database
3. **Queues for indexing** - Creates background jobs
4. **Submits to search engines** - Google and IndexNow
5. **Tracks status** - Updates database with results

### Check Indexing Status

#### Check if a URL is Indexed

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

#### Check Multiple Pages

Use the command to check status for multiple pages:

```bash
# Check specific URL
php artisan page-indexer:check-status "https://example.com/page"

# Check 100 pages
php artisan page-indexer:check-status --limit=100

# Check all pages (use with caution)
php artisan page-indexer:check-status --all

# Check with batches to avoid rate limiting
php artisan page-indexer:check-status --limit=100 --batch=10
```

### Working with Models

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

---

## 📊 Database Structure

The package creates 4 tables:

1. **`page_indexer_pages`** - Stores all pages to be indexed
2. **`page_indexer_jobs`** - Stores indexing job history
3. **`page_indexer_sitemaps`** - Stores sitemap information
4. **`page_indexer_status_history`** - Stores complete status timeline

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

## 📚 API Reference

### Facade Methods

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

## 🎯 Real-World Example

### Complete Setup Flow

```php
// 1. Configure your site URL in .env
// GOOGLE_SITE_URL=https://example.com/

// 2. List sites from Search Console (verify configuration)
php artisan page-indexer:sync-sites

// 3. Monitor sitemaps (discovers pages)
php artisan page-indexer:monitor-sitemaps

// 4. Enable auto-indexing in .env
// AUTO_INDEXING_ENABLED=true

// 5. Schedule commands in Kernel.php
$schedule->command('page-indexer:monitor-sitemaps')->daily();
$schedule->command('page-indexer:auto-index')->daily();

// That's it! Everything runs automatically.
```

### When a New Page is Published

```php
// After creating a new post/article
use Shammaa\LaravelPageIndexer\Facades\PageIndexer;

$newPageUrl = route('posts.show', $post);

// Option 1: Index immediately
PageIndexer::index($newPageUrl);

// Option 2: Let auto-indexing handle it (next run)
// Just publish the page, auto-indexing will pick it up
```

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

### Best Practices

1. **Use Queue** - Always use queue for bulk operations
2. **Respect Rate Limits** - Don't exceed Google's limits
3. **Monitor Status** - Check indexing status regularly
4. **Handle Errors** - Check job failures and retry if needed

---

## 🐛 Troubleshooting

### "Invalid Google token" Error

**Solution:**
1. Check Service Account JSON file path
2. Verify Service Account has Owner permissions in Search Console
3. Clear config cache: `php artisan config:clear`

### "Sitemap not found" Error

**Solution:**
1. Ensure sitemap is submitted in Google Search Console
2. Check sitemap URL is accessible
3. Run: `php artisan page-indexer:monitor-sitemaps --force`

### Pages Not Getting Indexed

**Check:**
1. Auto-indexing is enabled (`AUTO_INDEXING_ENABLED=true` in `.env`)
2. `GOOGLE_SITE_URL` is correctly set in `.env`
3. Queue worker is running
4. Check job failures: `php artisan queue:failed`
5. Check page status in database

---

## 📖 Documentation

- [Using with DataTable](DATATABLE_USAGE.md) - Complete guide for integrating with DataTable
- [How It Works](HOW_IT_WORKS.md) - Detailed technical explanation
- [Getting Started](GETTING_STARTED.md) - Step-by-step setup guide
- [API Documentation](https://github.com/shammaa/laravel-page-indexer/wiki)

---

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

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
