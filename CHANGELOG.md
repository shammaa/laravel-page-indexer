# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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

