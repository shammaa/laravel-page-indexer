# 🚀 Getting Started - Laravel Page Indexer

## ✅ ما تم إنشاؤه حتى الآن

1. ✅ **composer.json** - إعدادات المكتبة والـ dependencies
2. ✅ **config/page-indexer.php** - ملف الإعدادات الكامل
3. ✅ **HOW_IT_WORKS.md** - شرح شامل لكيفية عمل الأداة
4. ✅ **PLAN.md** - خطة العمل الكاملة
5. ✅ **هيكل المجلدات** - جاهز للبدء

---

## 📋 الخطوات التالية (ما تحتاج إنجازه)

### 1. Migrations (قاعدة البيانات)

أنشئ الملفات التالية:

**`database/migrations/2024_01_01_000001_create_sites_table.php`**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_indexer_sites', function (Blueprint $table) {
            $table->id();
            $table->string('google_site_url')->unique();
            $table->string('name');
            $table->boolean('auto_indexing_enabled')->default(false);
            $table->text('google_access_token')->nullable();
            $table->text('google_refresh_token')->nullable();
            $table->timestamp('google_token_expires_at')->nullable();
            $table->string('indexnow_api_key')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_indexer_sites');
    }
};
```

**`database/migrations/2024_01_01_000002_create_pages_table.php`**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_indexer_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained('page_indexer_sites')->onDelete('cascade');
            $table->text('url');
            $table->enum('indexing_status', ['pending', 'submitted', 'indexed', 'failed'])->default('pending');
            $table->timestamp('last_indexed_at')->nullable();
            $table->enum('indexing_method', ['google', 'indexnow', 'both'])->default('both');
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['site_id', 'indexing_status']);
            $table->unique(['site_id', 'url']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_indexer_pages');
    }
};
```

**`database/migrations/2024_01_01_000003_create_indexing_jobs_table.php`**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_indexer_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('page_indexer_pages')->onDelete('cascade');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->string('search_engine'); // google, bing, yandex, etc.
            $table->json('request_data')->nullable();
            $table->json('response_data')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            
            $table->index(['page_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_indexer_jobs');
    }
};
```

---

### 2. Models

**`src/Models/Site.php`**
```php
<?php

namespace Shammaa\LaravelPageIndexer\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    protected $table = 'page_indexer_sites';
    
    protected $fillable = [
        'google_site_url',
        'name',
        'auto_indexing_enabled',
        'google_access_token',
        'google_refresh_token',
        'google_token_expires_at',
        'indexnow_api_key',
        'settings',
    ];

    protected $casts = [
        'auto_indexing_enabled' => 'boolean',
        'google_token_expires_at' => 'datetime',
        'settings' => 'array',
    ];

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }
}
```

**`src/Models/Page.php`**
```php
<?php

namespace Shammaa\LaravelPageIndexer\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    protected $table = 'page_indexer_pages';
    
    protected $fillable = [
        'site_id',
        'url',
        'indexing_status',
        'last_indexed_at',
        'indexing_method',
        'metadata',
    ];

    protected $casts = [
        'last_indexed_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function indexingJobs(): HasMany
    {
        return $this->hasMany(IndexingJob::class);
    }
}
```

**`src/Models/IndexingJob.php`**
```php
<?php

namespace Shammaa\LaravelPageIndexer\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IndexingJob extends Model
{
    protected $table = 'page_indexer_jobs';
    
    protected $fillable = [
        'page_id',
        'status',
        'search_engine',
        'request_data',
        'response_data',
        'error_message',
        'processed_at',
    ];

    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
        'processed_at' => 'datetime',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}
```

---

### 3. Service Provider

**`src/LaravelPageIndexerServiceProvider.php`**

أنشئ Service Provider كامل (مشابه لمكتباتك الأخرى) يسجل:
- Config merging
- Service bindings
- Commands
- Routes (إذا كنت تريد dashboard)

---

### 4. Services (الخدمات الرئيسية)

أنشئ Services التالية:

1. **GoogleIndexingService** - للتعامل مع Google Indexing API
2. **SearchConsoleService** - للتعامل مع Google Search Console API
3. **IndexNowService** - للتعامل مع IndexNow API
4. **SitemapService** - لتحليل ومراقبة Sitemaps
5. **IndexingManager** - Service رئيسي ينسق كل شيء

---

### 5. Commands

1. **SyncSitesCommand** - مزامنة المواقع من Google Search Console
2. **MonitorSitemapsCommand** - مراقبة Sitemaps واكتشاف صفحات جديدة
3. **AutoIndexCommand** - فهرسة تلقائية للصفحات الجديدة

---

## 🔑 الإعدادات المطلوبة

### 1. Google Cloud Setup

1. اذهب إلى [Google Cloud Console](https://console.cloud.google.com/)
2. أنشئ مشروع جديد
3. فعل **Indexing API**
4. أنشئ **Service Account**
5. حمّل ملف JSON
6. أضف Service Account كـ **Owner** في Google Search Console

### 2. OAuth 2.0 Setup

1. أنشئ **OAuth 2.0 Client ID**
2. أضف Redirect URI
3. احفظ Client ID و Client Secret

### 3. IndexNow Setup

1. أنشئ API Key (32 حرف عشوائي)
2. أنشئ ملف `{api-key}.txt` في root موقعك
3. احفظ API Key في قاعدة البيانات

---

## 📝 ملاحظات مهمة

1. **Google Indexing API Limits:**
   - 200 URLs per day لكل موقع
   - يحتاج Owner permissions

2. **IndexNow:**
   - لا حدود رسمية لكن لا تفرط
   - يدعم عدة محركات بحث

3. **Queue:**
   - استخدم Queue للفهرسة الجماعية
   - تفادي Rate Limiting

---

## 🚀 الخطوات التالية

1. ✅ اكمل Migrations
2. ✅ اكمل Models
3. ✅ اكمل Service Provider
4. ✅ اكمل Services الأساسية
5. ✅ اكمل Commands
6. ✅ اختبر التكامل مع Google APIs
7. ✅ أنشئ Dashboard (اختياري)

---

## 📚 موارد مفيدة

- [Google Indexing API Documentation](https://developers.google.com/search/apis/indexing-api/v3/using-api)
- [Google Search Console API](https://developers.google.com/webmaster-tools/search-console-api-original)
- [IndexNow Protocol](https://www.indexnow.org/)

---

**ملاحظة:** هذا مشروع كبير! خذ وقتك وابدأ بالمكونات الأساسية أولاً. 🎯

