<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LearningPath;
use App\Models\LearningPathLesson;
use App\Models\LearningPathPractice;
use App\Models\LearningPathUnit;
use App\Models\Package;
use App\Models\User;
use App\Services\LearningPathAccessService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LearningPathController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage.courses');
    }

    public function index(): View
    {
        $paths = LearningPath::query()
            ->withCount(['units', 'lessons', 'practices', 'packages'])
            ->ordered()
            ->get();

        return view('admin.learning-paths.index', compact('paths'));
    }

    public function create(): View
    {
        $instructors = $this->instructorOptions();
        $packages = Package::query()->where('is_active', true)->orderBy('order')->orderBy('name')->get(['id', 'name']);

        return view('admin.learning-paths.create', compact('instructors', 'packages'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePath($request);
        $data['slug'] = $this->resolveSlug($data['slug'] ?? null, $data['title_en'] ?? $data['title_ar']);
        $data['is_active'] = $request->boolean('is_active');
        $data['is_published'] = $request->boolean('is_published');
        $data['is_sellable_standalone'] = $request->boolean('is_sellable_standalone');
        $data['sort_order'] = $data['sort_order'] ?? ((int) LearningPath::max('sort_order') + 1);
        $data['currency'] = strtoupper(trim((string) ($data['currency'] ?? 'QAR'))) ?: 'QAR';
        $packageIds = $data['packages'] ?? [];
        unset($data['packages']);

        $path = LearningPath::create($data);
        $this->syncPackages($path, $packageIds);

        return redirect()
            ->route('admin.learning-paths.show', $path)
            ->with('success', 'تم إنشاء المسار. أضف الوحدات ثم الدروس والأدوات/الأنشطة.');
    }

    public function show(LearningPath $learningPath): View
    {
        $learningPath->load([
            'units.lessons',
            'units.practices',
            'instructor:id,name,email',
            'packages:id,name,slug',
        ]);

        $learners = User::query()
            ->where('role', 'student')
            ->where('is_active', true)
            ->orderBy('name')
            ->limit(500)
            ->get(['id', 'name', 'email']);

        return view('admin.learning-paths.show', [
            'path' => $learningPath,
            'lessonTypes' => LearningPathLesson::TYPES,
            'practiceTypes' => LearningPathPractice::TYPES,
            'learners' => $learners,
        ]);
    }

    public function edit(LearningPath $learningPath): View
    {
        $learningPath->load('packages:id');

        return view('admin.learning-paths.edit', [
            'path' => $learningPath,
            'instructors' => $this->instructorOptions(),
            'packages' => Package::query()->where('is_active', true)->orderBy('order')->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, LearningPath $learningPath): RedirectResponse
    {
        $data = $this->validatePath($request, $learningPath->id);
        $data['slug'] = $this->resolveSlug(
            $data['slug'] ?? null,
            $data['title_en'] ?? $data['title_ar'],
            $learningPath->id
        );
        $data['is_active'] = $request->boolean('is_active');
        $data['is_published'] = $request->boolean('is_published');
        $data['is_sellable_standalone'] = $request->boolean('is_sellable_standalone');
        $data['currency'] = strtoupper(trim((string) ($data['currency'] ?? 'QAR'))) ?: 'QAR';
        $packageIds = $data['packages'] ?? [];
        unset($data['packages']);

        $learningPath->update($data);
        $this->syncPackages($learningPath, $packageIds);

        return redirect()
            ->route('admin.learning-paths.show', $learningPath)
            ->with('success', 'تم تحديث بيانات المسار وربطه بالباقات.');
    }

    public function destroy(LearningPath $learningPath): RedirectResponse
    {
        $learningPath->delete();

        return redirect()
            ->route('admin.learning-paths.index')
            ->with('success', 'تم حذف المسار وجميع وحداته.');
    }

    public function storeUnit(Request $request, LearningPath $learningPath): RedirectResponse
    {
        $data = $request->validate([
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'summary_ar' => ['nullable', 'string', 'max:500'],
            'summary_en' => ['nullable', 'string', 'max:500'],
            'estimated_minutes' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:99999'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order']
            ?? ((int) $learningPath->units()->max('sort_order') + 1);

        $learningPath->units()->create($data);

        return back()->with('success', 'تمت إضافة الوحدة.');
    }

    public function updateUnit(Request $request, LearningPathUnit $unit): RedirectResponse
    {
        $data = $request->validate([
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'summary_ar' => ['nullable', 'string', 'max:500'],
            'summary_en' => ['nullable', 'string', 'max:500'],
            'estimated_minutes' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:99999'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        $unit->update($data);

        return back()->with('success', 'تم تحديث الوحدة.');
    }

    public function destroyUnit(LearningPathUnit $unit): RedirectResponse
    {
        $unit->delete();

        return back()->with('success', 'تم حذف الوحدة.');
    }

    public function storeLesson(Request $request, LearningPathUnit $unit): RedirectResponse
    {
        $data = $request->validate([
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'content_type' => ['required', Rule::in(LearningPathLesson::TYPES)],
            'body_ar' => ['nullable', 'string'],
            'body_en' => ['nullable', 'string'],
            'video_url' => ['nullable', 'string', 'max:500'],
            'external_url' => ['nullable', 'string', 'max:500'],
            'duration_minutes' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:99999'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $data['is_preview'] = $request->boolean('is_preview');
        $data['sort_order'] = $data['sort_order']
            ?? ((int) $unit->lessons()->max('sort_order') + 1);

        $unit->lessons()->create($data);

        return back()->with('success', 'تمت إضافة الدرس/المحتوى.');
    }

    public function updateLesson(Request $request, LearningPathLesson $lesson): RedirectResponse
    {
        $data = $request->validate([
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'content_type' => ['required', Rule::in(LearningPathLesson::TYPES)],
            'body_ar' => ['nullable', 'string'],
            'body_en' => ['nullable', 'string'],
            'video_url' => ['nullable', 'string', 'max:500'],
            'external_url' => ['nullable', 'string', 'max:500'],
            'duration_minutes' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:99999'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $data['is_preview'] = $request->boolean('is_preview');

        $lesson->update($data);

        return back()->with('success', 'تم تحديث الدرس.');
    }

    public function destroyLesson(LearningPathLesson $lesson): RedirectResponse
    {
        $lesson->delete();

        return back()->with('success', 'تم حذف الدرس.');
    }

    public function storePractice(Request $request, LearningPathUnit $unit): RedirectResponse
    {
        $data = $request->validate([
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'summary_ar' => ['nullable', 'string', 'max:500'],
            'summary_en' => ['nullable', 'string', 'max:500'],
            'practice_type' => ['required', Rule::in(LearningPathPractice::TYPES)],
            'body_ar' => ['nullable', 'string'],
            'body_en' => ['nullable', 'string'],
            'resource_url' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:99999'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order']
            ?? ((int) $unit->practices()->max('sort_order') + 1);

        $unit->practices()->create($data);

        return back()->with('success', 'تمت إضافة التطبيق/الأداة.');
    }

    public function updatePractice(Request $request, LearningPathPractice $practice): RedirectResponse
    {
        $data = $request->validate([
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'summary_ar' => ['nullable', 'string', 'max:500'],
            'summary_en' => ['nullable', 'string', 'max:500'],
            'practice_type' => ['required', Rule::in(LearningPathPractice::TYPES)],
            'body_ar' => ['nullable', 'string'],
            'body_en' => ['nullable', 'string'],
            'resource_url' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:99999'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        $practice->update($data);

        return back()->with('success', 'تم تحديث التطبيق/الأداة.');
    }

    public function destroyPractice(LearningPathPractice $practice): RedirectResponse
    {
        $practice->delete();

        return back()->with('success', 'تم حذف التطبيق/الأداة.');
    }

    public function reorderUnit(Request $request, LearningPathUnit $unit): RedirectResponse
    {
        $this->reorderSibling($unit, LearningPathUnit::class, 'learning_path_id', (int) $unit->learning_path_id, $request->input('direction'));

        return back()->with('success', 'تم تحديث ترتيب الوحدة.');
    }

    public function reorderLesson(Request $request, LearningPathLesson $lesson): RedirectResponse
    {
        $this->reorderSibling($lesson, LearningPathLesson::class, 'learning_path_unit_id', (int) $lesson->learning_path_unit_id, $request->input('direction'));

        return back()->with('success', 'تم تحديث ترتيب الدرس.');
    }

    public function reorderPractice(Request $request, LearningPathPractice $practice): RedirectResponse
    {
        $this->reorderSibling($practice, LearningPathPractice::class, 'learning_path_unit_id', (int) $practice->learning_path_unit_id, $request->input('direction'));

        return back()->with('success', 'تم تحديث ترتيب العنصر.');
    }

    public function activateForLearner(Request $request, LearningPath $learningPath): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $learner = User::findOrFail($data['user_id']);
        if ($learner->role !== 'student') {
            return back()->withErrors(['user_id' => 'الحساب يجب أن يكون معلمًا متعلّمًا (متلقّي الخدمة).']);
        }

        LearningPathAccessService::activateStandalonePath($learningPath, $learner, null, $request->user());

        return back()->with('success', 'تم تفعيل المسار للمعلم «'.$learner->name.'».');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePath(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('learning_paths', 'slug')->ignore($ignoreId),
            ],
            'skill_focus_ar' => ['nullable', 'string', 'max:255'],
            'skill_focus_en' => ['nullable', 'string', 'max:255'],
            'summary_ar' => ['nullable', 'string', 'max:500'],
            'summary_en' => ['nullable', 'string', 'max:500'],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'estimated_minutes' => ['nullable', 'integer', 'min:1', 'max:99999'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'access_days' => ['nullable', 'integer', 'min:1', 'max:99999'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:99999'],
            'instructor_id' => ['nullable', 'exists:users,id'],
            'thumbnail' => ['nullable', 'string', 'max:500'],
            'packages' => ['nullable', 'array'],
            'packages.*' => ['integer', 'exists:packages,id'],
        ], [
            'title_ar.required' => 'عنوان المسار بالعربية مطلوب.',
        ]);
    }

    private function resolveSlug(?string $slug, string $fallback, ?int $ignoreId = null): string
    {
        $source = filled($slug) ? $slug : $fallback;

        return LearningPath::uniqueSlugFrom($source, $ignoreId);
    }

    /**
     * @param  array<int, int|string>  $packageIds
     */
    private function syncPackages(LearningPath $path, array $packageIds): void
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('package_learning_path')) {
            return;
        }

        $path->packages()->sync(
            collect($packageIds)->map(fn ($id) => (int) $id)->unique()->values()->all()
        );
    }

    /**
     * @param  class-string<Model>  $modelClass
     */
    private function reorderSibling(Model $item, string $modelClass, string $parentColumn, int $parentId, ?string $direction): void
    {
        $direction = $direction === 'up' ? 'up' : 'down';
        $siblings = $modelClass::query()
            ->where($parentColumn, $parentId)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $index = $siblings->search(fn ($row) => (int) $row->id === (int) $item->id);
        if ($index === false) {
            return;
        }

        $swapWith = $direction === 'up' ? $index - 1 : $index + 1;
        if ($swapWith < 0 || $swapWith >= $siblings->count()) {
            return;
        }

        $a = $siblings[$index];
        $b = $siblings[$swapWith];
        $tmp = $a->sort_order;
        $a->sort_order = $b->sort_order;
        $b->sort_order = $tmp;
        // If both equal, force distinct order by index
        if ((int) $a->sort_order === (int) $b->sort_order) {
            $a->sort_order = $index;
            $b->sort_order = $swapWith;
        }
        $a->save();
        $b->save();
    }

    private function instructorOptions()
    {
        return User::query()
            ->whereIn('role', ['instructor', 'teacher'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
    }
}
