<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\InstitutionMember;
use App\Models\InstitutionProgram;
use App\Models\User;
use App\Support\PlatformModules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Inquiry entry for Schools & Institutions (not direct checkout by default).
 */
class InstitutionInquiryController extends Controller
{
    public function create(): View
    {
        $this->ensureEnabled();

        return view('public.institutions.inquiry', [
            'serviceLabels' => InstitutionProgram::serviceLabels(),
            'orgTypes' => Institution::orgTypeLabels(),
            'deliveryModes' => InstitutionProgram::deliveryModes(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureEnabled();

        $data = $request->validate([
            'org_name' => ['required', 'string', 'max:255'],
            'org_type' => ['required', Rule::in(Institution::ORG_TYPES)],
            'contact_name' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'email', 'max:255'],
            'contact_phone' => ['required', 'string', 'max:40'],
            'city' => ['nullable', 'string', 'max:80'],
            'country' => ['nullable', 'string', 'max:80'],
            'service_key' => ['required', Rule::in(InstitutionProgram::serviceKeys())],
            'planned_participants' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'delivery_mode' => ['nullable', Rule::in(array_keys(InstitutionProgram::deliveryModes()))],
            'message' => ['nullable', 'string', 'max:5000'],
        ], [
            'org_name.required' => 'اسم الجهة مطلوب.',
            'service_key.required' => 'اختر نوع الخدمة.',
        ]);

        $institution = Institution::query()
            ->where('contact_email', $data['contact_email'])
            ->where('name_ar', $data['org_name'])
            ->first();

        if (! $institution) {
            $institution = Institution::create([
                'slug' => Institution::uniqueSlugFrom($data['org_name']),
                'name_ar' => $data['org_name'],
                'org_type' => $data['org_type'],
                'city' => $data['city'] ?? null,
                'country' => $data['country'] ?? null,
                'contact_name' => $data['contact_name'],
                'contact_email' => $data['contact_email'],
                'contact_phone' => $data['contact_phone'],
                'is_active' => true,
            ]);
        } else {
            $institution->update([
                'org_type' => $data['org_type'],
                'contact_name' => $data['contact_name'],
                'contact_phone' => $data['contact_phone'],
                'city' => $data['city'] ?? $institution->city,
                'country' => $data['country'] ?? $institution->country,
            ]);
        }

        $coordinatorUser = User::query()->where('email', $data['contact_email'])->first()
            ?: Auth::user();

        if ($coordinatorUser) {
            InstitutionMember::query()->updateOrCreate(
                [
                    'institution_id' => $institution->id,
                    'email' => $data['contact_email'],
                ],
                [
                    'user_id' => $coordinatorUser->id,
                    'member_role' => InstitutionMember::ROLE_COORDINATOR,
                    'name' => $data['contact_name'],
                    'phone' => $data['contact_phone'],
                    'is_active' => true,
                ]
            );
        }

        $isDev = in_array($data['service_key'], ['institutional_development', 'needs_assessment', 'followup_evaluation'], true);

        $program = InstitutionProgram::create([
            'institution_id' => $institution->id,
            'service_key' => $data['service_key'],
            'program_kind' => $isDev ? InstitutionProgram::KIND_DEVELOPMENT : InstitutionProgram::KIND_TRAINING,
            'title_ar' => ($isDev ? 'طلب تطوير: ' : 'طلب تدريب: ').($data['org_name']),
            'status' => InstitutionProgram::STATUS_INQUIRY,
            'planned_participants' => $data['planned_participants'] ?? null,
            'delivery_mode' => $data['delivery_mode'] ?? null,
            'inquiry_notes' => $data['message'] ?? null,
            'diagnosis_notes' => $isDev ? ($data['message'] ?? null) : null,
            'coordinator_user_id' => $coordinatorUser?->id,
            'currency' => platform_currency(),
            'progress_percent' => 0,
        ]);

        try {
            $serviceLabel = InstitutionProgram::serviceLabels()[$data['service_key']] ?? $data['service_key'];
            \App\Services\InquiryService::fromInstitutionInquiry(
                $institution,
                $data['contact_name'],
                $data['message'] ?? null,
                (string) $serviceLabel,
                (int) $program->id
            );
        } catch (\Throwable $e) {
            report($e);
        }

        $success = 'تم استلام طلبكم (Inquiry). سيتواصل فريق تدريس لاب بعرض (Proposal) وفق الاحتياج.';
        if ($coordinatorUser) {
            $success .= ' يمكنك متابعة البرامج من بوابة الجهة بعد تسجيل الدخول.';
        }

        return redirect()
            ->route('public.institutions.inquiry')
            ->with('success', $success);
    }

    private function ensureEnabled(): void
    {
        abort_unless(PlatformModules::enabled('schools_institutions'), 404);
    }
}
