# استخدام المكتبة مع DataTable - دليل سريع

## ✅ نعم، يمكنك إضافة زر فهرسة لكل مقالة في DataTable!

تم إضافة الميزات التالية:

### 1. **Trait للموديل** (`HasPageIndexing`)
أضف Trait إلى موديل المقالات:

```php
use Shammaa\LaravelPageIndexer\Traits\HasPageIndexing;

class Post extends Model
{
    use HasPageIndexing;
    
    protected function getRouteName(): string
    {
        return 'posts.show';
    }
}
```

### 2. **API Controller** جاهز
تم إنشاء Controller في: `src/Http/Controllers/Api/PageIndexingController.php`

### 3. **Helper Functions** متاحة
```php
$post->indexUrl($site, 'both', true); // فهرس المقالة
$post->checkIndexingStatus($site);    // تحقق من الحالة
$post->isIndexed();                    // هل مؤرشف؟
$post->getIndexingStatusBadge();       // Badge HTML
```

---

## 📝 الخطوات المطلوبة

### 1. إضافة Routes
أضف في `routes/api.php`:

```php
use Shammaa\LaravelPageIndexer\Http\Controllers\Api\PageIndexingController;

Route::prefix('api/page-indexer')->middleware('auth')->group(function () {
    Route::post('/index', [PageIndexingController::class, 'index']);
    Route::get('/status', [PageIndexingController::class, 'status']);
});
```

### 2. في Controller الخاص بك (DataTable)
```php
->addColumn('indexing_status', function ($post) {
    $page = $post->indexed_page;
    if (!$page) {
        return '<span class="badge badge-secondary">غير مرسل</span>';
    }
    
    $badges = [
        'indexed' => '<span class="badge badge-success">✅ مؤرشف</span>',
        'submitted' => '<span class="badge badge-info">⏳ تم الإرسال</span>',
        'pending' => '<span class="badge badge-warning">⏳ قيد الانتظار</span>',
        'failed' => '<span class="badge badge-danger">❌ فشل</span>',
    ];
    
    return $badges[$page->indexing_status] ?? '';
})
->addColumn('indexing_action', function ($post) {
    $url = route('posts.show', $post->slug);
    $isIndexed = $post->isIndexed();
    
    $btn = $isIndexed 
        ? '<button class="btn btn-success btn-sm" disabled>✅ مؤرشف</button>'
        : '<button class="btn btn-primary btn-sm" onclick="indexUrl(\''.$url.'\')">🚀 أرسل للفهرسة</button>';
    
    return $btn;
})
```

### 3. JavaScript Function
```javascript
function indexUrl(url) {
    $.ajax({
        url: '/api/page-indexer/index',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            url: url,
            method: 'both',
            queue: true
        },
        success: function(response) {
            if (response.success) {
                alert('تم الإرسال بنجاح!');
                $('#posts-table').DataTable().ajax.reload(null, false);
            }
        }
    });
}
```

---

## 📚 المزيد من التفاصيل

راجع ملف `DATATABLE_USAGE.md` للدليل الكامل مع أمثلة مفصلة!

---

## 🎯 الخلاصة

- ✅ **Trait جاهز**: `HasPageIndexing`
- ✅ **API Controller جاهز**: `PageIndexingController`
- ✅ **Helper Functions متاحة**: `indexUrl()`, `isIndexed()`, etc.
- ✅ **دليل كامل**: `DATATABLE_USAGE.md`

**كل ما تحتاجه هو إضافة Routes والكود JavaScript!**

