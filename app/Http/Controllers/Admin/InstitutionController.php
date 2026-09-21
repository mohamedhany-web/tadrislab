<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\InstitutionMember;
use App\Models\InstitutionProgram;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InstitutionController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            if (! $user || (! $user->isAdmin() && ! $user->hasPermission('manage.packages') && ! $user->hasPermission('manage.institutions'))) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function index(Request $request): View
    {
        $q = Institution::query()->withCount(['members', 'programs'])->latest();
        if ($request->filled('search')) {
            $s = '%'.$request->string('search').'%';
            $q->where(function ($w) use ($s) {
                $w->where('name_ar', 'like', $s)
                    ->orWhere('name_en', 'like', $s)
                    ->orWhere('contact_email', 'like', $s)
                    ->orWhere('city', 'like', $s);
            });
        }
        if ($request->filled('org_type') && in_array($request->org_type, Institution::ORG_TYPES, true)) {
            $q->where('org_type', $request->org_type);
        }

        $institutions = $q->paginate(25)->withQueryString();

        return view('admin.institutions.index', [
            'institutions' => $institutions,
            'orgTypes' => Institution::orgTypeLabels(),
            'filters' => $request->only(['search', 'org_type']),
        ]);
    }

    public function create(): View
    {
        return view('admin.institutions.create', [
            'institution' => null,
            'orgTypes' => Institution::orgTypeLabels(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['created_by'] = $request->user()->id;
        $data['slug'] = $data['slug'] ?: Institution::uniqueSlugFrom($data['name_en'] ?? $data['name_ar']);

        $institution = Institution::create($data);

        if ($request->filled('coordinator_name') || $request->filled('coordinator_user_id')) {
            InstitutionMember::create([
                'institution_id' => $institution->id,
                'user_id' => $request->input('coordinator_user_id') ?: null,
                'member_role' => InstitutionMember::ROLE_COORDINATOR,
                'name' => $request->input('coordinator_name') ?: $institution->contact_name,
                'email' => $request->input('coordinator_email') ?: $institution->contact_email,
                'phone' => $request->input('coordinator_phone') ?: $institution->contact_phone,
                'is_active' => true,
            ]);
        }

        return redirect()
            ->route('admin.institutions.show', $institution)
            ->with('success', 'تم إنشاء حساب الجهة.');
    }

    public function show(Institution $institution): View
    {
        $institution->load([
            'members' => fn ($q) => $q->orderByRaw("FIELD(member_role, 'coordinator', 'participant')")->orderBy('name'),
            'programs' => fn ($q) => $q->latest(),
            'createdBy:id,name',
        ]);

        $users = User::query()
            ->where('is_active', true)
            ->whereIn('role', ['student', 'instructor', 'teacher', 'admin'])
            ->orderBy('name')
            ->limit(400)
            ->get(['id', 'name', 'email', 'role']);

        return view('admin.institutions.show', [
            'institution' => $institution,
            'orgTypes' => Institution::orgTypeLabels(),
            'users' => $users,
            'serviceLabels' => InstitutionProgram::serviceLabels(),
            'statuses' => InstitutionProgram::statuses(),
        ]);
    }

    public function edit(Institution $institution): View
    {
        return view('admin.institutions.edit', [
            'institution' => $institution,
            'orgTypes' => Institution::orgTypeLabels(),
        ]);
    }

    public function update(Request $request, Institution $institution): RedirectResponse
    {
        $data = $this->validated($request, $institution->id);
        $data['is_active'] = $request->boolean('is_active');
        if (blank($data['slug'] ?? null)) {
            $data['slug'] = Institution::uniqueSlugFrom($data['name_en'] ?? $data['name_ar'], $institution->id);
        }
        $institution->update($data);

        return redirect()
            ->route('admin.institutions.show', $institution)
            ->with('success', 'تم تحديث بيانات الجهة.');
    }

    public function destroy(Institution $institution): RedirectResponse
    {
        $institution->update(['is_active' => false]);

        return redirect()
            ->route('admin.institutions.index')
            ->with('success', 'تم إيقاف حساب الجهة (لم يُحذف للحفاظ على البرامج).');
    }

    public function storeMember(Request $request, Institution $institution): RedirectResponse
    {
        $data = $request->validate([
            'member_role' => ['required', Rule::in([InstitutionMember::ROLE_COORDINATOR, InstitutionMember::ROLE_PARTICIPANT])],
            'user_id' => ['nullable', 'exists:users,id'],
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'title' => ['nullable', 'string', 'max:255'],
        ]);

        if (empty($data['user_id']) && blank($data['name'])) {
            return back()->withErrors(['name' => 'أدخل اسمًا أو اربط مستخدمًا.'])->withInput();
        }

        if (! empty($data['user_id'])) {
            $user = User::find($data['user_id']);
            $data['name'] = $data['name'] ?: $user?->name;
            $data['email'] = $data['email'] ?: $user?->email;
            $data['phone'] = $data['phone'] ?: $user?->phone;
        }

        $data['institution_id'] = $institution->id;
        $data['is_active'] = true;
        InstitutionMember::create($data);

        return back()->with('success', 'تمت إضافة العضو إلى الجهة.');
    }

    public function destroyMember(Institution $institution, InstitutionMember $member): RedirectResponse
    {
        abort_unless((int) $member->institution_id === (int) $institution->id, 404);
        $member->delete();

        return back()->with('success', 'تم حذف العضو.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('institutions', 'slug')->ignore($ignoreId)],
            'org_type' => ['required', Rule::in(Institution::ORG_TYPES)],
            'country' => ['nullable', 'string', 'max:80'],
            'city' => ['nullable', 'string', 'max:80'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:40'],
            'notes' => ['nullable', 'string'],
            'coordinator_user_id' => ['nullable', 'exists:users,id'],
            'coordinator_name' => ['nullable', 'string', 'max:255'],
            'coordinator_email' => ['nullable', 'email', 'max:255'],
            'coordinator_phone' => ['nullable', 'string', 'max:40'],
        ], [
            'name_ar.required' => 'اسم الجهة بالعربية مطلوب.',
        ]);
    }
}
