# تعليمات إصدار النسخة الجديدة v1.1.0

## الطريقة الآمنة (موصى بها) - الحفاظ على التاريخ

### 1. التأكد من أن كل التغييرات محفوظة
```powershell
git status
git add .
git commit -m "Release v1.1.0 - Simplified to single site configuration"
```

### 2. إنشاء Tag جديد
```powershell
git tag -a v1.1.0 -m "Release v1.1.0 - Simplified to single site configuration"
```

### 3. رفع التغييرات والـ Tag
```powershell
git push origin main
git push origin v1.1.0
```

أو استخدم السكريبت الجاهز:
```powershell
.\release.ps1
```

---

## الطريقة البديلة - حذف كل شيء وإعادة رفع (⚠️ خطير)

**تحذير:** هذه الطريقة ستحذف كل التاريخ من GitHub!

### 1. حذف الـ remote
```powershell
git remote remove origin
```

### 2. إضافة الـ remote من جديد
```powershell
git remote add origin https://github.com/shammaa/laravel-page-indexer.git
```

### 3. حذف كل الـ branches والـ tags من GitHub (من GitHub مباشرة أو):
```powershell
# حذف جميع الـ tags من GitHub
git push origin --delete $(git tag -l)

# حذف الـ main branch (سيتم إعادة إنشاؤه)
git push origin --delete main
```

### 4. رفع كل شيء من جديد
```powershell
git push -u origin main --force
git push origin --tags --force
```

---

## ملاحظات مهمة

1. **الطريقة الآمنة أفضل** - تحافظ على التاريخ والـ issues والـ pull requests
2. **v1.1.0** هو الإصدار الجديد (آخر إصدار كان v1.0.1)
3. بعد الرفع، أنشئ Release على GitHub من الـ tag الجديد
4. أضف Release Notes من CHANGELOG.md

---

## بعد الرفع

1. اذهب إلى GitHub → Releases
2. أنشئ Release جديد من tag v1.1.0
3. انسخ محتوى CHANGELOG.md للإصدار الجديد
4. حدد "Set as the latest release"

