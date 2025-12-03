# Using the Library with DataTable - Quick Guide

## ✅ Yes, you can add an indexing button for each article in DataTable!

The following features have been added:

### 1. **Trait for Model** (`HasPageIndexing`)
Add the Trait to your articles model:

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

### 2. **API Controller** Ready
Controller created at: `src/Http/Controllers/Api/PageIndexingController.php`

### 3. **Helper Functions** Available
```php
$post->indexUrl($site, 'both', true); // Index the article
$post->checkIndexingStatus($site);    // Check status
$post->isIndexed();                    // Is it indexed?
$post->getIndexingStatusBadge();       // Badge HTML
```

---

## 📝 Required Steps

### 1. Add Routes
Add in `routes/api.php`:

```php
use Shammaa\LaravelPageIndexer\Http\Controllers\Api\PageIndexingController;

Route::prefix('api/page-indexer')->middleware('auth')->group(function () {
    Route::post('/index', [PageIndexingController::class, 'index']);
    Route::get('/status', [PageIndexingController::class, 'status']);
});
```

### 2. In Your Controller (DataTable)
```php
->addColumn('indexing_status', function ($post) {
    $page = $post->indexed_page;
    if (!$page) {
        return '<span class="badge badge-secondary">Not Sent</span>';
    }
    
    $badges = [
        'indexed' => '<span class="badge badge-success">✅ Indexed</span>',
        'submitted' => '<span class="badge badge-info">⏳ Sent</span>',
        'pending' => '<span class="badge badge-warning">⏳ Pending</span>',
        'failed' => '<span class="badge badge-danger">❌ Failed</span>',
    ];
    
    return $badges[$page->indexing_status] ?? '';
})
->addColumn('indexing_action', function ($post) {
    $url = route('posts.show', $post->slug);
    $isIndexed = $post->isIndexed();
    
    $btn = $isIndexed 
        ? '<button class="btn btn-success btn-sm" disabled>✅ Indexed</button>'
        : '<button class="btn btn-primary btn-sm" onclick="indexUrl(\''.$url.'\')">🚀 Send for Indexing</button>';
    
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
                alert('Sent successfully!');
                $('#posts-table').DataTable().ajax.reload(null, false);
            }
        }
    });
}
```

---

## 📚 More Details

See the `DATATABLE_USAGE.md` file for the complete guide with detailed examples!

---

## 🎯 Summary

- ✅ **Trait Ready**: `HasPageIndexing`
- ✅ **API Controller Ready**: `PageIndexingController`
- ✅ **Helper Functions Available**: `indexUrl()`, `isIndexed()`, etc.
- ✅ **Complete Guide**: `DATATABLE_USAGE.md`

**All you need is to add Routes and JavaScript code!**

