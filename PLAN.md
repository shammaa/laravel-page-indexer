# خطة عمل Laravel Page Indexer

## المكونات الأساسية

### 1. Database Structure
- **sites** - المواقع المرتبطة
- **pages** - الصفحات المراقبة
- **indexing_jobs** - مهام الفهرسة
- **sitemaps** - خرائط المواقع
- **indexing_status_history** - تاريخ حالة الفهرسة

### 2. Services
- **GoogleIndexingService** - Google Indexing API
- **SearchConsoleService** - Google Search Console API
- **IndexNowService** - IndexNow API (Bing, Yandex, etc.)
- **SitemapService** - Parse & monitor sitemaps
- **IndexingManager** - Main service orchestrator

### 3. Jobs & Commands
- **ProcessIndexingJob** - Background job للفهرسة
- **SyncSitesCommand** - Sync Google Search Console
- **MonitorSitemapsCommand** - Monitor sitemaps
- **AutoIndexCommand** - Auto-index new pages

### 4. Controllers & Views
- Dashboard for managing sites
- Pages listing and filtering
- Status timeline
- Bulk operations

### 5. Configuration
- Google API credentials
- IndexNow endpoints
- Auto-indexing settings
- Queue configuration

## خطوات التنفيذ

### Phase 1: الأساسيات ✅
- [x] composer.json
- [x] Service Provider
- [x] Config file
- [ ] Migrations

### Phase 2: Models
- [ ] Site model
- [ ] Page model
- [ ] IndexingJob model
- [ ] Relationships

### Phase 3: Google APIs
- [ ] Google Indexing API integration
- [ ] Google Search Console API integration
- [ ] OAuth2 authentication

### Phase 4: IndexNow
- [ ] IndexNow API service
- [ ] Multiple search engines support

### Phase 5: Sitemap
- [ ] XML parser
- [ ] Sitemap monitor
- [ ] Auto-discovery

### Phase 6: Auto-Indexing
- [ ] Scheduled jobs
- [ ] Queue jobs
- [ ] Status tracking

### Phase 7: Dashboard
- [ ] Controllers
- [ ] Views
- [ ] API endpoints

## APIs المستخدمة

### Google Indexing API
```
POST https://indexing.googleapis.com/v3/urlNotifications:publish
```

### Google Search Console API
```
GET https://www.googleapis.com/webmasters/v3/sites
GET https://www.googleapis.com/webmasters/v3/sites/{siteUrl}/sitemaps
```

### IndexNow API
```
POST https://api.indexnow.org/IndexNow
```

## Dependencies المطلوبة

- google/apiclient - للـ Google APIs
- guzzlehttp/guzzle - للـ HTTP requests
- illuminate/queue - للـ background jobs

