<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\LearningPath;
use App\Models\Package;
use App\Models\TeacherTool;
use App\Models\TutoringGroupPackage;
use App\Models\User;
use App\Services\LearningPathAccessService;
use App\Services\PackageEntitlementService;
use App\Services\TutoringGroupPackagePricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PackageController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage.packages');
    }

    /**
     * مركز الباقات والأسعار: برامج مسجّلة + أسعار البرامج + باقات الحصص.
     */
    public function index(Request $request)
    {
        $showLegacyCatalogTabs = \App\Support\PlatformModules::enabled('tutoring')
            || (bool) config('admin_nav.show_legacy_ops', false);

        if (! $showLegacyCatalogTabs) {
            $request->merge(['tab' => 'packages']);
        }

        $activeTab = $request->get('tab', 'packages');
        if (! $showLegacyCatalogTabs) {
            $activeTab = 'packages';
        }

        $packageCounts = ['learningPaths'];
        if ($showLegacyCatalogTabs) {
            $packageCounts[] = 'courses';
        }

        $packagesQuery = Package::withCount($packageCounts)
            ->orderBy('order')
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $packagesQuery->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $packagesQuery->where('is_active', false);
            }
        }

        if ($request->filled('track')) {
            $packagesQuery->where(function ($q) use ($request) {
                $q->where('package_type', $request->track)
                    ->orWhere('track', $request->track);
            });
        }

        if ($request->filled('package_type')) {
            $packagesQuery->where('package_type', $request->package_type);
        }

        if ($request->filled('search') && $request->tab !== 'courses' && $request->tab !== 'tutoring') {
            $search = $request->search;
            $packagesQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('card_summary', 'like', "%{$search}%");
            });
        }

        $packages = $packagesQuery->paginate(20, ['*'], 'packages_page');

        $packageStats = [
            'total' => Package::count(),
            'active' => Package::where('is_active', true)->count(),
            'inactive' => Package::where('is_active', false)->count(),
            'featured' => Package::where('is_featured', true)->count(),
        ];

        $courses = collect();
        $programmingLanguages = collect();
        $categories = collect();
        $courseStats = ['total' => 0, 'free' => 0, 'paid' => 0, 'total_revenue' => 0];
        $tutoringPackages = collect();
        $tutoringStats = ['total' => 0, 'active' => 0, 'featured' => 0, 'avg_savings' => 0];
        $pricingTiers = [
            ['months' => 1, 'discount' => 0],
            ['months' => 3, 'discount' => 16.7],
            ['months' => 6, 'discount' => 20],
            ['months' => 12, 'discount' => 25],
        ];
        $exampleCalc = TutoringGroupPackagePricingService::calculate(10, 8, 3, 200);

        if ($showLegacyCatalogTabs) {
            $coursesQuery = AdvancedCourse::with(['instructor'])
                ->withCount('lessons')
                ->orderBy('created_at', 'desc');

            if ($request->filled('course_status')) {
                if ($request->course_status === 'free') {
                    $coursesQuery->where(function ($q) {
                        $q->where('is_free', true)->orWhere('price', 0);
                    });
                } elseif ($request->course_status === 'paid') {
                    $coursesQuery->where('is_free', false)->where('price', '>', 0);
                }
            }

            if ($request->filled('course_level')) {
                $coursesQuery->where('level', $request->course_level);
            }

            if ($request->filled('course_language')) {
                $coursesQuery->where('programming_language', $request->course_language);
            }

            if ($request->filled('course_category')) {
                $coursesQuery->where('category', $request->course_category);
            }

            if ($request->filled('course_active')) {
                $coursesQuery->where('is_active', $request->course_active === '1');
            }

            if ($request->filled('course_search') && $request->tab === 'courses') {
                $search = $request->course_search;
                $coursesQuery->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('programming_language', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                });
            }

            $courses = $coursesQuery->paginate(12, ['*'], 'courses_page');

            $programmingLanguages = AdvancedCourse::whereNotNull('programming_language')
                ->distinct()
                ->pluck('programming_language')
                ->sort()
                ->values();

            $categories = AdvancedCourse::whereNotNull('category')
                ->distinct()
                ->pluck('category')
                ->sort()
                ->values();

            $courseStats = [
                'total' => AdvancedCourse::count(),
                'free' => AdvancedCourse::where(function ($q) {
                    $q->where('is_free', true)->orWhere('price', 0);
                })->count(),
                'paid' => AdvancedCourse::where('is_free', false)->where('price', '>', 0)->count(),
                'total_revenue' => AdvancedCourse::where('is_free', false)->sum('price'),
            ];

            $tutoringPackagesQuery = TutoringGroupPackage::query()
                ->with(['tutoringGroup.instructor:id,name'])
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')
                ->orderBy('duration_months');

            if ($request->filled('tutoring_status')) {
                if ($request->tutoring_status === 'active') {
                    $tutoringPackagesQuery->where('is_active', true);
                } elseif ($request->tutoring_status === 'inactive') {
                    $tutoringPackagesQuery->where('is_active', false);
                }
            }

            if ($request->filled('tutoring_search') && $request->tab === 'tutoring') {
                $search = $request->tutoring_search;
                $tutoringPackagesQuery->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhereHas('tutoringGroup', function ($gq) use ($search) {
                            $gq->where('title', 'like', "%{$search}%");
                        });
                });
            }

            $tutoringPackages = $tutoringPackagesQuery->paginate(20, ['*'], 'tutoring_page');

            $tutoringStats = [
                'total' => TutoringGroupPackage::count(),
                'active' => TutoringGroupPackage::where('is_active', true)->count(),
                'featured' => TutoringGroupPackage::where('is_featured', true)->count(),
                'avg_savings' => (int) round((float) (TutoringGroupPackage::query()
                    ->whereNotNull('original_price')
                    ->where('original_price', '>', 0)
                    ->whereColumn('original_price', '>', 'price')
                    ->selectRaw('AVG(((original_price - price) / NULLIF(original_price, 0)) * 100) as avg_pct')
                    ->value('avg_pct') ?? 0)),
            ];
        }

        return view('admin.packages.index', compact(
            'packages',
            'courses',
            'packageStats',
            'courseStats',
            'programmingLanguages',
            'categories',
            'tutoringPackages',
            'tutoringStats',
            'pricingTiers',
            'exampleCalc',
            'showLegacyCatalogTabs',
            'activeTab'
        ));
    }

    public function create()
    {
        $courses = AdvancedCourse::where('is_active', true)
            ->orderBy('title')
            ->get(['id', 'title', 'price']);

        $learningPaths = LearningPath::query()
            ->where('is_active', true)
            ->ordered()
            ->get(['id', 'title_ar', 'title_en', 'slug', 'skill_focus_ar']);

        $teacherTools = TeacherTool::query()
            ->when(
                \Illuminate\Support\Facades\Schema::hasColumn('teacher_tools', 'is_active'),
                fn ($q) => $q->where('is_active', true)
            )
            ->orderBy('title_ar')
            ->get(['id', 'title_ar', 'slug']);

        return view('admin.packages.create', compact('courses', 'learningPaths', 'teacherTools'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatePackage($request);
        $courseIds = $validated['courses'] ?? [];
        $pathIds = $validated['learning_paths'] ?? [];
        $toolIds = $validated['teacher_tools'] ?? [];

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated = $this->normalizePackagePayload($validated, $request);

        $package = Package::create($validated);
        $this->syncCourses($package, $courseIds);
        $this->syncLearningPaths($package, $pathIds);
        $this->syncTeacherTools($package, $toolIds);

        return redirect()->route('admin.packages.show', $package)
            ->with('success', 'تم إنشاء الباقة بنجاح — قابلة للتعديل بالكامل من الأدمن.');
    }

    public function show(Package $package)
    {
        $package->load(['courses', 'learningPaths', 'teacherTools']);
        $learners = User::query()
            ->where('role', 'student')
            ->where('is_active', true)
            ->orderBy('name')
            ->limit(500)
            ->get(['id', 'name', 'email']);

        return view('admin.packages.show', compact('package', 'learners'));
    }

    public function edit(Package $package)
    {
        $package->load(['courses', 'learningPaths', 'teacherTools']);
        $courses = AdvancedCourse::where('is_active', true)
            ->orderBy('title')
            ->get(['id', 'title', 'price']);
        $learningPaths = LearningPath::query()
            ->where('is_active', true)
            ->ordered()
            ->get(['id', 'title_ar', 'title_en', 'slug', 'skill_focus_ar']);

        $teacherTools = TeacherTool::query()
            ->when(
                \Illuminate\Support\Facades\Schema::hasColumn('teacher_tools', 'is_active'),
                fn ($q) => $q->where('is_active', true)
            )
            ->orderBy('title_ar')
            ->get(['id', 'title_ar', 'slug']);

        return view('admin.packages.edit', compact('package', 'courses', 'learningPaths', 'teacherTools'));
    }

    public function update(Request $request, Package $package)
    {
        $validated = $this->validatePackage($request, $package);
        $courseIds = $validated['courses'] ?? [];
        $pathIds = $validated['learning_paths'] ?? [];
        $toolIds = $validated['teacher_tools'] ?? [];

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated = $this->normalizePackagePayload($validated, $request, $package);

        $package->update($validated);
        $this->syncCourses($package, $courseIds);
        $this->syncLearningPaths($package, $pathIds);
        $this->syncTeacherTools($package, $toolIds);

        return redirect()->route('admin.packages.show', $package)
            ->with('success', 'تم تحديث الباقة بنجاح');
    }

    /**
     * تفعيل الباقة لمعلم: مسارات + جلسات استشارة + أدوات/مقاعد.
     */
    public function activateForLearner(Request $request, Package $package)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $learner = User::findOrFail($data['user_id']);
        if ($learner->role !== 'student') {
            return back()->withErrors(['user_id' => 'المستخدم يجب أن يكون معلمًا متعلّمًا (حساب متلقّي الخدمة).']);
        }

        $package->load('learningPaths');
        $hasPaths = $package->learningPaths->isNotEmpty();
        $hasConsult = (int) ($package->consultation_sessions ?? 0) > 0;
        if (! $hasPaths && ! $hasConsult && ! $package->includes_tools) {
            return back()->withErrors(['user_id' => 'اربط مسارات أو جلسات استشارة أو أدوات قبل التفعيل.']);
        }

        $result = PackageEntitlementService::activateForUser(
            $package,
            $learner,
            null,
            $request->user()
        );

        $parts = [];
        $parts[] = count($result['path_enrollments']).' مسار';
        if ($result['entitlement']) {
            $parts[] = (int) $result['entitlement']->consultation_sessions_total.' جلسة استشارة';
            if ($result['entitlement']->includes_tools) {
                $parts[] = 'أدوات وموارد';
            }
        }

        return back()->with('success', 'تم تفعيل الباقة للمعلم «'.$learner->name.'»: '.implode(' · ', $parts).'.');
    }

    public function destroy(Package $package)
    {
        if ($package->thumbnail) {
            \App\Services\PublicMediaStorage::delete($package->thumbnail);
        }

        $package->delete();

        return redirect()->route('admin.packages.index')
            ->with('success', 'تم حذف الباقة بنجاح');
    }

    public function updatePrice(Request $request, AdvancedCourse $course)
    {
        $validated = $request->validate([
            'price' => 'required|numeric|min:0',
            'is_free' => 'boolean',
        ]);

        $course->update([
            'price' => $validated['price'],
            'is_free' => $validated['is_free'] ?? ($validated['price'] == 0),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث السعر بنجاح',
                'course' => $course->fresh(),
            ]);
        }

        return redirect()->route('admin.packages.index', ['tab' => 'courses'])
            ->with('success', 'تم تحديث السعر بنجاح');
    }

    public function updateBulkPrices(Request $request)
    {
        $validated = $request->validate([
            'courses' => 'required|array',
            'courses.*.id' => 'required|exists:advanced_courses,id',
            'courses.*.price' => 'required|numeric|min:0',
            'courses.*.is_free' => 'boolean',
        ]);

        foreach ($validated['courses'] as $courseData) {
            AdvancedCourse::where('id', $courseData['id'])->update([
                'price' => $courseData['price'],
                'is_free' => $courseData['is_free'] ?? ($courseData['price'] == 0),
            ]);
        }

        return redirect()->route('admin.packages.index', ['tab' => 'courses'])
            ->with('success', 'تم تحديث الأسعار بنجاح');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePackage(Request $request, ?Package $package = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('packages', 'slug')->ignore($package?->id),
            ],
            'description' => 'nullable|string',
            'card_summary' => 'nullable|string',
            'features' => 'nullable|array',
            'features.*' => 'nullable|string|max:500',
            'tools_resources' => 'nullable|array',
            'tools_resources.*' => 'nullable|string|max:500',
            'discount_note' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'track' => 'nullable|string|max:40',
            'package_type' => ['nullable', 'string', Rule::in(Package::typeKeys())],
            'consultation_sessions' => 'nullable|integer|min:0|max:1000',
            'participant_seats' => 'nullable|integer|min:0|max:10000',
            'includes_tools' => 'boolean',
            'cta_mode' => ['nullable', 'string', Rule::in(['register', 'contact', 'quote'])],
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:'.config('upload_limits.max_upload_kb'),
            'duration_days' => 'nullable|integer|min:0',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_popular' => 'boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'courses' => 'nullable|array',
            'courses.*' => 'exists:advanced_courses,id',
            'learning_paths' => 'nullable|array',
            'learning_paths.*' => 'exists:learning_paths,id',
            'teacher_tools' => 'nullable|array',
            'teacher_tools.*' => 'exists:teacher_tools,id',
        ]);
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function normalizePackagePayload(array $validated, Request $request, ?Package $package = null): array
    {
        $validated['card_summary'] = $validated['card_summary'] ?? null;
        if ($validated['card_summary'] !== null) {
            $validated['card_summary'] = trim($validated['card_summary']) ?: null;
        }

        $validated['currency'] = strtoupper(trim((string) ($validated['currency'] ?? 'QAR'))) ?: 'QAR';
        $validated['package_type'] = $validated['package_type'] ?? Package::TYPE_INDIVIDUAL;
        $validated['track'] = $validated['package_type']; // keep legacy column aligned
        $validated['cta_mode'] = $validated['cta_mode']
            ?? (in_array($validated['package_type'], [Package::TYPE_SCHOOL, Package::TYPE_CUSTOM], true) ? 'contact' : 'register');
        $validated['includes_tools'] = $request->boolean('includes_tools');
        $validated['consultation_sessions'] = $validated['consultation_sessions'] ?? null;
        $validated['participant_seats'] = $validated['participant_seats'] ?? null;
        $validated['discount_note'] = filled($validated['discount_note'] ?? null) ? trim((string) $validated['discount_note']) : null;
        $validated['features'] = collect($validated['features'] ?? [])
            ->map(fn ($f) => trim((string) $f))
            ->filter()
            ->values()
            ->all();
        $validated['tools_resources'] = collect($validated['tools_resources'] ?? [])
            ->map(fn ($f) => trim((string) $f))
            ->filter()
            ->values()
            ->all();
        if ($validated['tools_resources'] !== []) {
            $validated['includes_tools'] = true;
        }
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_popular'] = $request->boolean('is_popular');

        unset($validated['learning_paths'], $validated['courses'], $validated['teacher_tools']);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = \App\Services\PublicMediaStorage::store(
                $request->file('thumbnail'),
                'packages',
                $package?->thumbnail
            );
        }

        return $validated;
    }

    /**
     * @param  array<int, int|string>  $courseIds
     */
    private function syncCourses(Package $package, array $courseIds): void
    {
        $coursesData = [];
        foreach ($courseIds as $index => $courseId) {
            $coursesData[$courseId] = ['order' => $index];
        }
        $package->courses()->sync($coursesData);
    }

    /**
     * @param  array<int, int|string>  $pathIds
     */
    private function syncLearningPaths(Package $package, array $pathIds): void
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('package_learning_path')) {
            return;
        }

        $package->learningPaths()->sync(
            collect($pathIds)->map(fn ($id) => (int) $id)->unique()->values()->all()
        );
    }

    /**
     * @param  array<int, int|string>  $toolIds
     */
    private function syncTeacherTools(Package $package, array $toolIds): void
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('package_teacher_tool')) {
            return;
        }

        $sync = [];
        foreach (collect($toolIds)->map(fn ($id) => (int) $id)->unique()->values()->all() as $i => $id) {
            $sync[$id] = ['sort_order' => $i];
        }
        $package->teacherTools()->sync($sync);

        if ($sync !== []) {
            $package->forceFill(['includes_tools' => true])->saveQuietly();
        }
    }
}
