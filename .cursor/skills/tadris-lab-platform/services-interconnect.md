# ترابط الخدمات — تدريس لاب

## الأدوار (لغة المنتج)

| الحساب | نسمّيه | الوظيفة |
|--------|--------|---------|
| متلقّي الخدمة | **معلم** | يستكشف الخدمات ويشترك عبر الباقات (في DB قد يبقى role `student` مؤقتًا) |
| مقدّم الخدمة | **مدرب** | يُفعَّل لتدريس كورس موصّف و/أو تقديم خدمة (مسار، استشارة، ورشة، أدوات) |
| الوصول | **باقة** | تجمع المسارات + الأدوات + الاستشارات + المقاعد |

## كيف يُوصَّف المحتوى؟

- **كورس / مسار:** المدرب يكتب العنوان، الهدف، المخرجات، الأنشطة، وربط الأدوات.
- **استشارة:** نوع الجلسة، النطاق، المخرجات المكتوبة.
- **أداة/مورد:** اسم الأداة، حالة الاستخدام، خطوات التطبيق.
- **برنامج مؤسسي:** النطاق، المقاعد، الجدول، التقارير.

## أين في الكود؟

- خريطة الربط: `config/tadris_services.php`
- المحرّك: `app/Support/TadrisServicePages.php`
- القالب: `resources/views/public/site/show.blade.php`
- النصوص: `lang/ar|en/site.php` (`actors`, `delivery`, `pages.*`)

## صلاحيات المدرب (أدمن)

من **مدربو الأكاديمية → صفحة المدرب** يمكن:

1. تفعيل الحساب (`is_active`)
2. تفعيل صلاحيات التقديم (`instructor_grants_enabled`) — مفتاح رئيسي
3. اختيار **خدمات مسموحة** (`instructor_service_assignments` ← مفاتيح `config/tadris_services.grantable_services`)
4. اختيار **كورسات مسموحة** (`instructor_course_assignments`؛ إن كان الكورس بلا مالك يُربَط `advanced_courses.instructor_id`)

التحقق في الكود: `User::instructorDeliveryEnabled()`, `grantedServiceKeys()`, `canDeliverService()`, `teachingAdvancedCourseIds()`.

| السطح | المسار |
|-------|--------|
| واجهة الأدمن | `admin/academy-instructors/{id}` |
| حفظ الصلاحيات | `PUT …/grants` → `admin.academy-instructors.grants.update` |

## الحلقة التشغيلية (باقة → مسار → مدرب ↔ معلم)

```
أدمن يربط مسارات في الباقة (package_learning_path)
  → يفعّل الباقة لمعلم متعلّم (LearningPathAccessService → teacher_path_enrollments)
  → المعلم يرى المسارات في /my-learning-paths

أدمن يمنح المدرب خدمة learning_paths + مسارات محددة
  (instructor_learning_path_assignments)
  → المدرب يرى المسارات في /instructor/learning-paths
  → يرى المعلمين المسجّلين عبر الباقات
```

| السطح | المسار |
|-------|--------|
| ربط مسارات بالباقة | أدمن → باقة → تعديل / تفاصيل |
| تفعيل لمعلم | `POST admin/packages/{id}/activate-learner` |
| لوحة المعلم | `/my-learning-paths` |
| منح المدرب | مدربو الأكاديمية → صلاحيات → مسارات مسموحة |
| لوحة المدرب | `/instructor/learning-paths` |

## حسابات تجريبية

| الدور | البريد | كلمة المرور |
|-------|--------|-------------|
| أدمن | `admin@tadrislab.com` | `password123` |
| مدرب | `instructor@tadrislab.com` | `TadrisInstructor@2026` (مسارات ممنوحة) |
| معلم متعلّم | `teacher.learner@tadrislab.com` | `password123` (مسار مفعّل عبر باقة) |
