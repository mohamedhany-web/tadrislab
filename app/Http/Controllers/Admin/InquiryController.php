<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConsultationRequest;
use App\Models\Inquiry;
use App\Models\Institution;
use App\Models\InstitutionProgram;
use App\Models\Order;
use App\Models\User;
use App\Services\InquiryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InquiryController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage.contact-messages');
    }

    public function index(Request $request): View
    {
        $query = Inquiry::query()->with(['user:id,name,email', 'assignee:id,name'])->latest('inquired_at');

        if ($request->filled('search')) {
            $term = '%'.$request->string('search')->toString().'%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('phone', 'like', $term)
                    ->orWhere('reference', 'like', $term)
                    ->orWhere('subject', 'like', $term)
                    ->orWhere('message', 'like', $term);
            });
        }

        $query->status($request->input('status'))->ofType($request->input('type'));

        if ($request->filled('source')) {
            $query->where('source', $request->string('source')->toString());
        }

        $inquiries = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => Inquiry::count(),
            'new' => Inquiry::where('status', Inquiry::STATUS_NEW)->count(),
            'in_progress' => Inquiry::where('status', Inquiry::STATUS_IN_PROGRESS)->count(),
            'resolved' => Inquiry::where('status', Inquiry::STATUS_RESOLVED)->count(),
        ];

        return view('admin.inquiries.index', [
            'inquiries' => $inquiries,
            'stats' => $stats,
            'typeLabels' => Inquiry::typeLabels(),
            'statusLabels' => Inquiry::statusLabels(),
            'sourceLabels' => Inquiry::sourceLabels(),
        ]);
    }

    public function create(): View
    {
        return view('admin.inquiries.create', $this->formMeta());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['source'] = $data['source'] ?? Inquiry::SOURCE_ADMIN;
        if ($data['source'] === Inquiry::SOURCE_WHATSAPP) {
            $inquiry = InquiryService::fromWhatsApp($data);
        } else {
            $inquiry = InquiryService::create($data);
        }

        return redirect()
            ->route('admin.inquiries.show', $inquiry)
            ->with('success', 'تم تسجيل الاستفسار '.$inquiry->reference);
    }

    public function show(Inquiry $inquiry): View
    {
        $inquiry->load([
            'user:id,name,email',
            'order:id,amount,status,currency',
            'consultationRequest:id,status,preferred_slot_at,student_id',
            'institution:id,name_ar,contact_email',
            'institutionProgram:id,title_ar,status,institution_id',
            'assignee:id,name',
            'contactMessage',
        ]);

        return view('admin.inquiries.show', array_merge($this->formMeta(), [
            'inquiry' => $inquiry,
        ]));
    }

    public function update(Request $request, Inquiry $inquiry): RedirectResponse
    {
        $data = $this->validated($request, updating: true);

        $inquiry->update([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'inquiry_type' => $data['inquiry_type'],
            'status' => $data['status'],
            'source' => $data['source'] ?? $inquiry->source,
            'user_id' => $data['user_id'] ?? null,
            'order_id' => $data['order_id'] ?? null,
            'consultation_request_id' => $data['consultation_request_id'] ?? null,
            'institution_id' => $data['institution_id'] ?? null,
            'institution_program_id' => $data['institution_program_id'] ?? null,
            'subject' => $data['subject'] ?? null,
            'message' => $data['message'] ?? null,
            'admin_notes' => $data['admin_notes'] ?? null,
            'assigned_to' => $data['assigned_to'] ?? null,
            'inquired_at' => $data['inquired_at'] ?? $inquiry->inquired_at,
        ]);

        return redirect()
            ->route('admin.inquiries.show', $inquiry)
            ->with('success', 'تم تحديث الاستفسار.');
    }

    public function destroy(Inquiry $inquiry): RedirectResponse
    {
        $inquiry->delete();

        return redirect()
            ->route('admin.inquiries.index')
            ->with('success', 'تم حذف الاستفسار.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formMeta(): array
    {
        return [
            'typeLabels' => Inquiry::typeLabels(),
            'statusLabels' => Inquiry::statusLabels(),
            'sourceLabels' => Inquiry::sourceLabels(),
            'staff' => User::query()
                ->whereIn('role', ['admin', 'employee'])
                ->where('is_active', true)
                ->orderBy('name')
                ->limit(200)
                ->get(['id', 'name', 'email']),
            'recentOrders' => Order::query()->latest('id')->limit(40)->get(['id', 'amount', 'status', 'user_id']),
            'recentBookings' => ConsultationRequest::query()->latest('id')->limit(40)->get(['id', 'status', 'student_id']),
            'institutions' => Institution::query()->orderBy('name_ar')->limit(100)->get(['id', 'name_ar']),
            'institutionPrograms' => InstitutionProgram::query()
                ->orderByDesc('id')
                ->limit(120)
                ->get(['id', 'title_ar', 'institution_id', 'status']),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, bool $updating = false): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'inquiry_type' => ['required', Rule::in(Inquiry::typeKeys())],
            'status' => [$updating ? 'required' : 'nullable', Rule::in(array_keys(Inquiry::statusLabels()))],
            'source' => ['nullable', Rule::in(array_keys(Inquiry::sourceLabels()))],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:20000'],
            'admin_notes' => ['nullable', 'string', 'max:20000'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'order_id' => ['nullable', 'integer', 'exists:orders,id'],
            'consultation_request_id' => ['nullable', 'integer', 'exists:consultation_requests,id'],
            'institution_id' => ['nullable', 'integer', 'exists:institutions,id'],
            'institution_program_id' => ['nullable', 'integer', 'exists:institution_programs,id'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
            'inquired_at' => ['nullable', 'date'],
        ]);
    }
}
