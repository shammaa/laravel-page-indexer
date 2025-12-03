# استخدام المكتبة مع DataTable

هذا الدليل يوضح كيفية استخدام `laravel-page-indexer` مع DataTable لإضافة أزرار فهرسة وعرض حالة الفهرس لكل مقالة.

---

## 📋 المتطلبات

- Laravel 9.0+
- المكتبة: `shammaa/laravel-page-indexer`
- DataTable (أي مكتبة: jQuery DataTables, Laravel DataTables, etc.)

---

## 🚀 الخطوة 1: إضافة Trait للموديل

أضف Trait `HasPageIndexing` إلى موديل المقالات:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Shammaa\LaravelPageIndexer\Traits\HasPageIndexing;

class Post extends Model
{
    use HasPageIndexing;

    protected $fillable = ['title', 'slug', 'content'];

    /**
     * Override this method if your route name is different
     */
    protected function getRouteName(): string
    {
        return 'posts.show';
    }

    /**
     * Override this method if you want a custom URL
     */
    public function getIndexableUrl(): string
    {
        return route('posts.show', $this->slug);
    }
}
```

---

## 🔌 الخطوة 2: إضافة Routes للـ API

أضف routes في `routes/api.php` أو `routes/web.php`:

```php
use Shammaa\LaravelPageIndexer\Http\Controllers\Api\PageIndexingController;

Route::prefix('api/page-indexer')->middleware('auth')->group(function () {
    Route::post('/index', [PageIndexingController::class, 'index']);
    Route::get('/status', [PageIndexingController::class, 'status']);
    Route::post('/bulk-index', [PageIndexingController::class, 'bulkIndex']);
});
```

---

## 💻 الخطوة 3: إضافة الأعمدة في DataTable

### مثال باستخدام Laravel DataTables (yajra/laravel-datatables)

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Shammaa\LaravelPageIndexer\Models\Site;
use Yajra\DataTables\Facades\DataTables;

class PostController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $posts = Post::with(['indexed_page'])->select('posts.*');
            
            return DataTables::of($posts)
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

                    $badge = $badges[$page->indexing_status] ?? '<span class="badge badge-secondary">غير معروف</span>';
                    
                    if ($page->last_indexed_at) {
                        $badge .= '<br><small class="text-muted">' . $page->last_indexed_at->diffForHumans() . '</small>';
                    }

                    return $badge;
                })
                ->addColumn('indexing_action', function ($post) {
                    $url = route('posts.show', $post->slug);
                    $page = $post->indexed_page;
                    $isIndexed = $page && $page->isIndexed();
                    
                    $btnClass = $isIndexed ? 'btn-success' : 'btn-primary';
                    $btnText = $isIndexed ? '✅ مؤرشف' : '🚀 أرسل للفهرسة';
                    $btnDisabled = $isIndexed ? 'disabled' : '';

                    return sprintf(
                        '<button class="btn btn-sm %s %s" data-url="%s" onclick="indexUrl(this, \'%s\')">
                            %s
                        </button>',
                        $btnClass,
                        $btnDisabled,
                        $url,
                        $url,
                        $btnText
                    );
                })
                ->rawColumns(['indexing_status', 'indexing_action'])
                ->make(true);
        }

        return view('admin.posts.index');
    }
}
```

---

## 🎨 الخطوة 4: إضافة JavaScript للـ DataTable

أضف الكود التالي في Blade view:

```html
<!-- DataTable -->
<table id="posts-table" class="table table-striped">
    <thead>
        <tr>
            <th>العنوان</th>
            <th>حالة الفهرس</th>
            <th>الإجراءات</th>
        </tr>
    </thead>
</table>

<script>
$(document).ready(function() {
    var table = $('#posts-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.posts.index') }}",
        columns: [
            { data: 'title', name: 'title' },
            { data: 'indexing_status', name: 'indexing_status', orderable: false, searchable: false },
            { data: 'indexing_action', name: 'indexing_action', orderable: false, searchable: false },
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/ar.json'
        }
    });
});

// دالة إرسال URL للفهرسة
function indexUrl(button, url) {
    if ($(button).hasClass('disabled')) {
        return;
    }

    $(button).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> جاري الإرسال...');

    $.ajax({
        url: '/api/page-indexer/index',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            url: url,
            method: 'both',
            queue: true // Queue for background processing
        },
        success: function(response) {
            if (response.success) {
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'تم الإرسال بنجاح!',
                    text: response.message || 'تم إرسال المقالة للفهرسة بنجاح',
                    timer: 2000,
                    showConfirmButton: false
                });

                // Update button
                $(button).removeClass('btn-primary').addClass('btn-success')
                    .html('✅ تم الإرسال').prop('disabled', true);

                // Reload table to update status
                $('#posts-table').DataTable().ajax.reload(null, false);
            } else {
                throw new Error(response.error || 'حدث خطأ');
            }
        },
        error: function(xhr) {
            var error = xhr.responseJSON?.error || 'حدث خطأ أثناء الإرسال';
            
            Swal.fire({
                icon: 'error',
                title: 'خطأ!',
                text: error
            });

            $(button).prop('disabled', false).html('🚀 أرسل للفهرسة');
        }
    });
}

// دالة التحقق من حالة الفهرس
function checkIndexingStatus(url) {
    $.ajax({
        url: '/api/page-indexer/status',
        method: 'GET',
        data: {
            url: url,
            check_google: true // Check via Google Search Console for accurate status
        },
        success: function(response) {
            if (response.success) {
                var status = response.data.status;
                var badge = '';

                switch(status) {
                    case 'indexed':
                        badge = '<span class="badge badge-success">✅ مؤرشف</span>';
                        break;
                    case 'submitted':
                        badge = '<span class="badge badge-info">⏳ تم الإرسال</span>';
                        break;
                    case 'pending':
                        badge = '<span class="badge badge-warning">⏳ قيد الانتظار</span>';
                        break;
                    case 'failed':
                        badge = '<span class="badge badge-danger">❌ فشل</span>';
                        break;
                    default:
                        badge = '<span class="badge badge-secondary">غير معروف</span>';
                }

                // Update status in table
                $('#posts-table').DataTable().ajax.reload(null, false);
            }
        }
    });
}

// التحقق من حالة الفهرس كل 30 ثانية للمقالات المعلقة
setInterval(function() {
    $('[data-status="pending"], [data-status="submitted"]').each(function() {
        var url = $(this).data('url');
        if (url) {
            checkIndexingStatus(url);
        }
    });
}, 30000);
</script>
```

---

## 📊 الخطوة 5: مثال كامل مع Blade View

```blade
@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <h2>المقالات</h2>
            <button class="btn btn-primary" onclick="bulkIndex()">🚀 فهرس جميع المقالات المعلقة</button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <table id="posts-table" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>العنوان</th>
                                <th>حالة الفهرس</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ... الكود السابق ...
</script>
@endpush
```

---

## 🔧 الخطوة 6: استخدام Helper Functions

يمكنك أيضاً استخدام Helper Functions مباشرة في Controller:

```php
use function Shammaa\LaravelPageIndexer\index_page;
use function Shammaa\LaravelPageIndexer\check_indexing_status;
use function Shammaa\LaravelPageIndexer\is_url_indexed;

// في Controller
$site = Site::first();

// Index a URL
$result = index_page($post->getIndexableUrl(), $site, 'both');

// Check if indexed
if (is_url_indexed($post->getIndexableUrl(), $site)) {
    // URL is indexed
}

// Check status
$status = check_indexing_status($post->getIndexableUrl(), $site);
```

---

## 🎯 الخطوة 7: استخدام Trait Methods

```php
// في Controller أو Model
$post = Post::find(1);

// Index URL
$result = $post->indexUrl($site, 'both', true); // queue = true

// Check status
$status = $post->checkIndexingStatus($site);

// Get badge HTML
$badge = $post->getIndexingStatusBadge();

// Check if indexed
if ($post->isIndexed()) {
    // Post is indexed
}
```

---

## ⚠️ ملاحظات مهمة

1. **Queue Processing**: استخدم `queue: true` للعمليات الضخمة لتفادي إبطاء الاستجابة
2. **Rate Limiting**: Google Indexing API له حد 200 URL يومياً
3. **Background Jobs**: تأكد من تشغيل `php artisan queue:work`
4. **Site Configuration**: تأكد من إعداد Site بشكل صحيح قبل الاستخدام

---

## 🔄 تحديث تلقائي للحالة

للتحديث التلقائي للحالة، يمكنك استخدام Polling:

```javascript
// تحديث حالة الفهرس كل دقيقة
setInterval(function() {
    $('#posts-table').DataTable().ajax.reload(null, false);
}, 60000);
```

أو استخدام WebSockets للحصول على تحديثات فورية.

---

## 📚 المزيد من الأمثلة

راجع ملف `README.md` للتفاصيل الكاملة حول جميع الميزات المتاحة.

