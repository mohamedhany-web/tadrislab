<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConsultationService;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ConsultationServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage.consultations');
    }

    public function index(): View
    {
        $services = ConsultationService::query()->ordered()->with('defaultInstructor:id,name')->get();
        $typeLabels = ConsultationService::typeLabels();

        return view('admin.consultations.services.index', compact('services', 'typeLabels'));
    }

    public function create(): View
    {
        return view('admin.consultations.services.create', [
            'service' => null,
            'typeLabels' => ConsultationService::typeLabels(),
            'instructors' => $this->instructors(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['is_published'] = $request->boolean('is_published');
        $data['is_mvp'] = $request->boolean('is_mvp', true);
        $data['requires_instructor'] = $request->boolean('requires_instructor', true);
        $data['currency'] = strtoupper(trim((string) ($data['currency'] ?? 'QAR'))) ?: 'QAR';
        $data['slug'] = $data['slug'] ?: ConsultationService::uniqueSlugFrom($data['title_en'] ?? $data['title_ar']);
        $data['sort_order'] = $data['sort_order'] ?? ((int) ConsultationService::max('sort_order') + 10);

        $service = ConsultationService::create($data);

        return redirect()
            ->route('admin.consultations.services.index')
            ->with('success', 'تم إنشاء الخدمة الاستشارية: '.$service->title_ar);
    }

    public function edit(ConsultationService $service): View
    {
        return view('admin.consultations.services.edit', [
            'service' => $service,
            'typeLabels' => ConsultationService::typeLabels(),
            'instructors' => $this->instructors(),
        ]);
    }

    public function update(Request $request, ConsultationService $service): RedirectResponse
    {
        $data = $this->validated($request, $service->id);
        $data['is_active'] = $request->boolean('is_active');
        $data['is_published'] = $request->boolean('is_published');
        $data['is_mvp'] = $request->boolean('is_mvp');
        $data['requires_instructor'] = $request->boolean('requires_instructor');
        $data['currency'] = strtoupper(trim((string) ($data['currency'] ?? 'QAR'))) ?: 'QAR';
        if (blank($data['slug'] ?? null)) {
            $data['slug'] = ConsultationService::uniqueSlugFrom($data['title_en'] ?? $data['title_ar'], $service->id);
        }

        $service->update($data);

        return redirect()
            ->route('admin.consultations.services.index')
            ->with('success', 'تم تحديث الخدمة.');
    }

    public function destroy(ConsultationService $service): RedirectResponse
    {
        if ($service->requests()->exists()) {
            $service->update(['is_active' => false, 'is_published' => false]);

            return back()->with('success', 'للخدمة حجوزات — تم إيقافها بدل الحذف.');
        }

        $service->delete();

        return back()->with('success', 'تم حذف الخدمة.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('consultation_services', 'slug')->ignore($ignoreId)],
            'consultation_type' => ['required', Rule::in(ConsultationService::allowedTypes())],
            'summary_ar' => ['nullable', 'string', 'max:1000'],
            'summary_en' => ['nullable', 'string', 'max:1000'],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:480'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'default_instructor_id' => ['nullable', 'exists:users,id'],
        ], [
            'title_ar.required' => 'عنوان الخدمة بالعربية مطلوب.',
            'consultation_type.required' => 'نوع الاستشارة مطلوب.',
        ]);
    }

    private function instructors()
    {
        return User::query()
            ->whereIn('role', ['instructor', 'teacher'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
    }
}
