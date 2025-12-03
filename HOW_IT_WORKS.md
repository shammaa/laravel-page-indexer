# كيف تعمل أداة Page Indexer؟ 

## النظرة العامة

الأداة تقوم بأتمتة عملية فهرسة صفحات موقعك في محركات البحث مثل Google, Bing, Yandex.

---

## المكونات الرئيسية

### 1. Google Indexing API 🔍

**ما هي؟**
- API رسمية من Google لإرسال إشعارات عن صفحات جديدة أو محدثة
- تقوم بإخطار Google مباشرة بدلاً من الانتظار للزحف الطبيعي

**كيف تعمل؟**
```php
// 1. الحصول على Access Token من Google OAuth
$client = new Google_Client();
$client->setAuthConfig('path/to/service-account.json');
$client->addScope('https://www.googleapis.com/auth/indexing');
$accessToken = $client->fetchAccessTokenWithAssertion()['access_token'];

// 2. إرسال طلب الفهرسة
$httpClient = new \GuzzleHttp\Client();
$response = $httpClient->post('https://indexing.googleapis.com/v3/urlNotifications:publish', [
    'headers' => [
        'Authorization' => 'Bearer ' . $accessToken,
        'Content-Type' => 'application/json',
    ],
    'json' => [
        'url' => 'https://example.com/page',
        'type' => 'URL_UPDATED', // أو 'URL_DELETED'
    ],
]);
```

**المطلوب:**
- Service Account من Google Cloud
- تفعيل Indexing API في Google Cloud Console
- إضافة Service Account كـ Owner في Google Search Console

---

### 2. Google Search Console API 📊

**ما هي؟**
- API للوصول لبيانات Google Search Console
- تسمح بجلب المواقع، Sitemaps، وحالة الفهرسة

**كيف تعمل؟**
```php
// 1. الحصول على Access Token
$client = new Google_Client();
$client->setAuthConfig('path/to/oauth-credentials.json');
$client->addScope('https://www.googleapis.com/auth/webmasters.readonly');
// ... OAuth flow

// 2. جلب المواقع
$service = new Google_Service_Webmasters($client);
$sites = $service->sites->listSites();

// 3. جلب Sitemaps
$sitemaps = $service->sitemaps->listSitemaps('https://example.com/');

// 4. التحقق من حالة الفهرسة
$inspection = $service->urlInspection_index->inspect([
    'inspectionUrl' => 'https://example.com/page',
    'siteUrl' => 'https://example.com/',
]);
```

---

### 3. IndexNow API 🚀

**ما هي؟**
- بروتوكول مفتوح المصدر لإخطار محركات البحث بالتغييرات
- يدعم Bing, Yandex, Naver, Seznam, وغيرها

**كيف تعمل؟**
```php
// إرسال إشعار IndexNow
$httpClient = new \GuzzleHttp\Client();

// Bing
$httpClient->post('https://api.indexnow.org/IndexNow', [
    'json' => [
        'host' => 'example.com',
        'key' => 'your-api-key',
        'urlList' => [
            'https://example.com/page1',
            'https://example.com/page2',
        ],
    ],
]);

// Yandex
$httpClient->post('https://yandex.com/indexnow', [
    'json' => [
        'host' => 'example.com',
        'key' => 'your-api-key',
        'urlList' => [
            'https://example.com/page1',
        ],
    ],
]);
```

**المطلوب:**
- API Key (يتم إنشاؤها في موقعك)
- ملف `{api-key}.txt` في root الموقع للتحقق

---

### 4. Sitemap Parser 📝

**ما هي؟**
- تحليل ملفات XML Sitemap
- استخراج جميع URLs
- اكتشاف Sitemaps الجديدة

**كيف تعمل؟**
```php
// 1. جلب ملف Sitemap
$xml = file_get_contents('https://example.com/sitemap.xml');
$sitemap = simplexml_load_string($xml);

// 2. استخراج URLs
$urls = [];
foreach ($sitemap->url as $url) {
    $urls[] = (string) $url->loc;
}

// 3. اكتشاف Sitemaps متعددة
if (isset($sitemap->sitemap)) {
    foreach ($sitemap->sitemap as $subSitemap) {
        // معالجة sitemap فرعي
    }
}
```

---

### 5. Auto-Indexing System ⚡

**كيف يعمل؟**

1. **Scheduled Command** يعمل يومياً:
   ```php
   // في Kernel.php
   $schedule->command('page-indexer:auto-index')->daily();
   ```

2. **Workflow:**
   ```
   For each active site:
     - Fetch sitemap URLs
     - Compare with database
     - Find new URLs
     - Queue indexing jobs
     - Check indexing status
     - Update database
   ```

3. **Queue Jobs:**
   ```php
   // ProcessIndexingJob
   public function handle(IndexingManager $manager) {
       // Submit to Google
       $manager->submitToGoogle($this->url);
       
       // Submit to IndexNow
       $manager->submitToIndexNow($this->url);
       
       // Update status
       $this->page->update(['indexing_status' => 'submitted']);
   }
   ```

---

## Database Schema

### sites
```sql
- id
- google_site_url (https://example.com/)
- name
- auto_indexing_enabled
- google_access_token
- google_refresh_token
- indexnow_api_key
- created_at
- updated_at
```

### pages
```sql
- id
- site_id
- url
- indexing_status (pending, submitted, indexed, failed)
- last_indexed_at
- indexing_method (google, indexnow, both)
- created_at
- updated_at
```

### indexing_jobs
```sql
- id
- page_id
- status (pending, processing, completed, failed)
- search_engine (google, bing, yandex, etc.)
- response_data (JSON)
- error_message
- created_at
- updated_at
```

### indexing_status_history
```sql
- id
- page_id
- status
- search_engine
- checked_at
- created_at
```

---

## التدفق الكامل

### 1. الإعداد الأولي
```
المستخدم → يضيف Google OAuth credentials
       → يضيف Service Account
       → يوافق على الصلاحيات
       → النظام يحصل على Access Token
```

### 2. مزامنة المواقع
```
Command → يتصل بـ Google Search Console API
      → يجلب جميع المواقع
      → يحفظها في قاعدة البيانات
      → يجلب Sitemaps لكل موقع
```

### 3. اكتشاف الصفحات
```
Scheduled Job → يقرأ Sitemaps
            → يقارن مع قاعدة البيانات
            → يكتشف صفحات جديدة
            → ينشئ سجلات في جدول pages
```

### 4. الفهرسة التلقائية
```
Scheduled Job → لكل صفحة جديدة:
             → يرسل إلى Google Indexing API
             → يرسل إلى IndexNow API
             → يحفظ الـ response
             → يحدث حالة الصفحة
```

### 5. مراقبة الحالة
```
Scheduled Job → يتصل بـ Google Search Console API
            → يتحقق من حالة الفهرسة
            → يحدث قاعدة البيانات
            → يسجل في Timeline
```

---

## الأمان

### Google Service Account
- ملف JSON آمن (لا يتم رفعه على Git)
- مخزن في `storage/` مع حماية من الوصول المباشر
- Access Token يتم تحديثه تلقائياً

### IndexNow API Key
- مفتاح فريد لكل موقع
- يتم التحقق عبر ملف `.txt` في root الموقع
- يمكن تجديده في أي وقت

---

## الحدود والقيود

### Google Indexing API
- **200 URLs per day** لكل موقع
- يحتاج Owner permissions في Search Console
- للـ Job Posting و Video فقط (أو للمواقع المصدقة)

### IndexNow
- **10,000 URLs per request**
- **لا حدود يومية** (لكن لا تفرط)
- يدعم عدة محركات بحث

---

## الخلاصة

الأداة تقوم بـ:
1. ✅ ربط Google Search Console
2. ✅ جلب Sitemaps تلقائياً
3. ✅ اكتشاف صفحات جديدة
4. ✅ إرسالها للفهرسة تلقائياً
5. ✅ مراقبة حالة الفهرسة
6. ✅ دعم محركات بحث متعددة

النتيجة: **فهرسة أسرع = ظهور أسرع في نتائج البحث = حركة مرور أكثر** 🚀

