<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LearningPath;
use App\Models\Package;
use App\Models\TeacherTool;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TeacherToolController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage.courses');
    }

    public function index(Request $request): View
    {
        $query = TeacherTool::query()->ordered();

        if ($request->filled('type')) {
            $query->ofType($request->string('type')->toString());
        }
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'published') {
                $query->where('is_published', true);
            }
        }
        if ($request->filled('search')) {
            $term = '%'.$request->string('search')->toString().'%';
            $query->where(function ($q) use ($term) {
                $q->where('title_ar', 'like', $term)
                    ->orWhere('title_en', 'like', $term)
                    ->orWhere('slug', 'like', $term);
            });
        }

        $tools = $query->withCount(['learningPaths', 'packages'])->paginate(20)->withQueryString();

        return view('admin.teacher-tools.index', [
            'tools' => $tools,
            'typeLabels' => TeacherTool::typeLabels(),
        ]);
    }

    public function create(): View
    {
        return view('admin.teacher-tools.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data = $this->applyFlags($request, $data);
        $data = $this->storeUploads($request, $data);

        $tool = TeacherTool::create($data);
        $this->syncRelations($request, $tool);

        return redirect()
            ->route('admin.teacher-tools.show', $tool)
            ->with('success', 'تم إنشاء الأداة/المورد: '.$tool->title_ar);
    }

    public function show(TeacherTool $teacherTool): View
    {
        $teacherTool->load(['learningPaths', 'packages']);

        return view('admin.teacher-tools.show', [
            'tool' => $teacherTool,
            'typeLabels' => TeacherTool::typeLabels(),
            'accessLabels' => TeacherTool::accessModeLabels(),
        ]);
    }

    public function edit(TeacherTool $teacherTool): View
    {
        $teacherTool->load(['learningPaths', 'packages']);

        return view('admin.teacher-tools.edit', array_merge($this->formData(), [
            'tool' => $teacherTool,
        ]));
    }

    public function update(Request $request, TeacherTool $teacherTool): RedirectResponse
    {
        $data = $this->validated($request, $teacherTool->id);
        $data = $this->applyFlags($request, $data);
        $data = $this->storeUploads($request, $data, $teacherTool);

        $teacherTool->update($data);
        $this->syncRelations($request, $teacherTool);

        return redirect()
            ->route('admin.teacher-tools.show', $teacherTool)
            ->with('success', 'تم تحديث الأداة/المورد.');
    }

    public function destroy(TeacherTool $teacherTool): RedirectResponse
    {
        if ($teacherTool->file_path) {
            Storage::disk('public')->delete($teacherTool->file_path);
        }
        if ($teacherTool->thumbnail) {
            Storage::disk('public')->delete($teacherTool->thumbnail);
        }
        $teacherTool->delete();

        return redirect()
            ->route('admin.teacher-tools.index')
            ->with('success', 'تم حذف الأداة/المورد.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(): array
    {
        return [
            'typeLabels' => TeacherTool::typeLabels(),
            'accessLabels' => TeacherTool::accessModeLabels(),
            'learningPaths' => LearningPath::query()->where('is_active', true)->orderBy('sort_order')->get(['id', 'title_ar', 'slug']),
            'packages' => Package::query()->where('is_active', true)->orderBy('order')->orderBy('id')->get(['id', 'name', 'slug']),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'slug' => [
                'nullable', 'string', 'max:160',
                Rule::unique('teacher_tools', 'slug')->ignore($ignoreId),
            ],
            'tool_type' => ['required', 'string', Rule::in(TeacherTool::typeKeys())],
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'summary_ar' => ['nullable', 'string', 'max:500'],
            'summary_en' => ['nullable', 'string', 'max:500'],
            'description_ar' => ['nullable', 'string', 'max:20000'],
            'description_en' => ['nullable', 'string', 'max:20000'],
            'external_url' => ['nullable', 'url', 'max:500'],
            'access_mode' => ['required', 'string', Rule::in(array_keys(TeacherTool::accessModeLabels()))],
            'price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:8'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:99999'],
            'thumbnail' => ['nullable', 'image', 'max:4096'],
            'file' => ['nullable', 'file', 'max:20480'],
            'learning_paths' => ['nullable', 'array'],
            'learning_paths.*' => ['integer', 'exists:learning_paths,id'],
            'packages' => ['nullable', 'array'],
            'packages.*' => ['integer', 'exists:packages,id'],
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function applyFlags(Request $request, array $data): array
    {
        $data['is_active'] = $request->boolean('is_active', true);
        $data['is_published'] = $request->boolean('is_published');
        $data['is_standalone_product'] = $request->boolean('is_standalone_product');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['currency'] = filled($data['currency'] ?? null)
            ? normalize_currency($data['currency'])
            : platform_currency();
        unset($data['learning_paths'], $data['packages'], $data['thumbnail'], $data['file']);

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function storeUploads(Request $request, array $data, ?TeacherTool $existing = null): array
    {
        if ($request->hasFile('thumbnail')) {
            if ($existing?->thumbnail) {
                Storage::disk('public')->delete($existing->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('teacher-tools/thumbs', 'public');
        }

        if ($request->hasFile('file')) {
            if ($existing?->file_path) {
                Storage::disk('public')->delete($existing->file_path);
            }
            $file = $request->file('file');
            $data['file_path'] = $file->store('teacher-tools/files', 'public');
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_mime'] = $file->getClientMimeType();
            $data['file_size'] = $file->getSize();
        }

        return $data;
    }

    private function syncRelations(Request $request, TeacherTool $tool): void
    {
        $pathIds = collect($request->input('learning_paths', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values();
        $packageIds = collect($request->input('packages', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values();

        $pathSync = [];
        foreach ($pathIds as $i => $id) {
            $pathSync[$id] = ['sort_order' => ($i + 1) * 10];
        }
        $packageSync = [];
        foreach ($packageIds as $i => $id) {
            $packageSync[$id] = ['sort_order' => ($i + 1) * 10];
        }

        $tool->learningPaths()->sync($pathSync);
        $tool->packages()->sync($packageSync);

        // Keep package JSON labels in sync for public cards that still read tools_resources.
        foreach (Package::query()->whereIn('id', $packageIds)->get() as $package) {
            $labels = $package->teacherTools()->pluck('title_ar')->filter()->values()->all();
            if ($labels !== []) {
                $package->update([
                    'tools_resources' => $labels,
                    'includes_tools' => true,
                ]);
            }
        }
    }
}
