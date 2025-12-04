# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.1.0] - 2024-12-XX

### Changed
- **BREAKING:** Simplified to single site configuration via config file
- **BREAKING:** Removed multi-site support - now works with one website per installation
- **BREAKING:** Site configuration moved from database to `.env` file
- Removed `Site` model and `page_indexer_sites` table
- Removed `site_id` from `pages` and `sitemaps` tables
- Updated all commands to work without `--site-id` option
- Updated all API methods to remove `Site` parameter
- Simplified configuration - site settings now in `config/page-indexer.php`

### Added
- `GOOGLE_SITE_URL` environment variable for site configuration
- Site configuration section in config file
- Simplified API - no need to pass Site object

### Removed
- `Site` model
- `page_indexer_sites` migration
- Multi-site database structure
- `--site-id` option from all commands
- `--site-url` option from bulk-import command

### Migration Guide
If upgrading from v1.0.x:
1. Set `GOOGLE_SITE_URL` in your `.env` file
2. Remove any code that uses `Site` model
3. Update all `PageIndexer::index()` calls to remove `Site` parameter
4. Run migrations to update database structure (removes `site_id` columns)

## [1.0.1] - 2024-XX-XX

## [1.0.0] - 2024-01-01

### Added
- Initial release
- Google Indexing API integration
- Google Search Console API integration
- IndexNow API support (Bing, Yandex, Naver, etc.)
- Sitemap parsing and monitoring
- Auto-indexing system
- Status tracking and history
- Queue support for background processing
- Multiple commands:
  - `page-indexer:sync-sites` - Sync sites from Google Search Console
  - `page-indexer:monitor-sitemaps` - Monitor sitemaps and discover new pages
  - `page-indexer:auto-index` - Automatically index pending pages
- Database models:
  - Site model
  - Page model
  - IndexingJob model
  - Sitemap model
  - IndexingStatusHistory model
- Services:
  - GoogleIndexingService
  - SearchConsoleService
  - IndexNowService
  - SitemapService
  - IndexingManager
- Helper functions and Facade
- Comprehensive configuration file
- Complete documentation

### Features
- Multi-site support
- Automatic sitemap discovery
- Bulk indexing operations
- Rate limiting support
- Error handling and logging
- Status timeline tracking

