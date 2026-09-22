<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AdvancedCourse;
use App\Models\InstructorCourseAssignment;
use App\Models\InstructorLearningPathAssignment;
use App\Models\InstructorServiceAssignment;
use App\Models\LearningPath;
use App\Models\StudentInstructorAssignment;
use App\Models\TutoringGroup;
use App\Models\TutoringGroupBooking;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AcademyInstructorController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $instructors = User::query()
            ->whereIn('role', ['instructor', 'teacher'])
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'phone', 'is_active', 'profile_image']);

        $groupStats = TutoringGroup::query()
            ->selectRaw('instructor_id, type, count(*) as total')
            ->where('is_active', true)
            ->groupBy('instructor_id', 'type')
            ->get()
            ->groupBy('instructor_id');

        $courseCounts = AdvancedCourse::query()
            ->selectRaw('instructor_id, count(*) as total')
            ->whereNotNull('instructor_id')
            ->where('is_active', true)
            ->groupBy('instructor_id')
            ->pluck('total', 'instructor_id');

        $assignmentCounts = collect();
        if (Schema::hasTable('student_instructor_assignments')) {
            $assignmentCounts = StudentInstructorAssignment::query()
                ->where('status', StudentInstructorAssignment::STATUS_ACTIVE)
                ->selectRaw('instructor_id, count(*) as total')
                ->groupBy('instructor_id')
                ->pluck('total', 'instructor_id');
        }

        $rows = $instructors->map(function (User $instructor) use ($groupStats, $courseCounts, $assignmentCounts) {
            $byType = $groupStats->get($instructor->id, collect());
            $collective = (int) optional($byType->firstWhere('type', TutoringGroup::TYPE_COLLECTIVE))->total;
            $individual = (int) optional($byType->firstWhere('type', TutoringGroup::TYPE_INDIVIDUAL))->total;

            return [
                'instructor' => $instructor,
                'collective_groups' => $collective,
                'individual_groups' => $individual,
                'courses' => (int) ($courseCounts[$instructor->id] ?? 0),
                'assigned_students' => (int) ($assignmentCounts[$instructor->id] ?? 0),
            ];
        });

        $summary = [
            'instructors' => $rows->count(),
            'collective' => $rows->sum('collective_groups'),
            'individual' => $rows->sum('individual_groups'),
            'assignments' => $rows->sum('assigned_students'),
        ];

        return view('admin.academy-instructors.index', compact('rows', 'summary', 'search'));
    }

    public function show(User $instructor): View
    {
        abort_unless($instructor->isInstructor() || $instructor->isTeacher(), 404);

        $collectiveGroups = TutoringGroup::query()
            ->where('instructor_id', $instructor->id)
            ->collective()
            ->withCount('cohorts')
            ->orderBy('title')
            ->get();

        $individualGroups = TutoringGroup::query()
            ->where('instructor_id', $instructor->id)
            ->individual()
            ->withCount('packages')
            ->orderBy('title')
            ->get();

        $courses = AdvancedCourse::query()
            ->whereIn('id', $instructor->teachingAdvancedCourseIds()->all() ?: [0])
            ->with(['academicSubject:id,name', 'academicYear:id,name'])
            ->orderBy('title')
            ->get();

        $allCourses = AdvancedCourse::query()
            ->where('is_active', true)
            ->orderBy('title')
            ->get(['id', 'title', 'instructor_id']);

        $grantedCourseIds = Schema::hasTable('instructor_course_assignments')
            ? InstructorCourseAssignment::query()
                ->where('user_id', $instructor->id)
                ->where('is_active', true)
                ->pluck('advanced_course_id')
                ->map(fn ($id) => (int) $id)
                ->all()
            : [];

        $allLearningPaths = Schema::hasTable('learning_paths')
            ? LearningPath::query()->where('is_active', true)->ordered()->get(['id', 'title_ar', 'slug', 'instructor_id', 'skill_focus_ar'])
            : collect();

        $grantedPathIds = Schema::hasTable('instructor_learning_path_assignments')
            ? InstructorLearningPathAssignment::query()
                ->where('user_id', $instructor->id)
                ->where('is_active', true)
                ->pluck('learning_path_id')
                ->map(fn ($id) => (int) $id)
                ->all()
            : [];

        $grantableServices = config('tadris_services.grantable_services', []);
        $grantedServiceKeys = $instructor->grantedServiceKeys();

        $assignments = Schema::hasTable('student_instructor_assignments')
            ? StudentInstructorAssignment::query()
                ->where('instructor_id', $instructor->id)
                ->with(['student:id,name,email,phone', 'academicYear:id,name', 'assignedBy:id,name'])
                ->latest()
                ->get()
            : collect();

        $upcomingBookings = TutoringGroupBooking::query()
            ->where('instructor_id', $instructor->id)
            ->where('status', TutoringGroupBooking::STATUS_CONFIRMED)
            ->where('starts_at', '>=', now())
            ->with(['tutoringGroup:id,title,type', 'user:id,name'])
            ->orderBy('starts_at')
            ->limit(10)
            ->get();

        $students = User::query()
            ->where('role', 'student')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $years = AcademicYear::orderBy('order')->orderBy('name')->get(['id', 'name']);

        return view('admin.academy-instructors.show', compact(
            'instructor',
            'collectiveGroups',
            'individualGroups',
            'courses',
            'allCourses',
            'grantedCourseIds',
            'allLearningPaths',
            'grantedPathIds',
            'grantableServices',
            'grantedServiceKeys',
            'assignments',
            'upcomingBookings',
            'students',
            'years'
        ));
    }

    public function updateGrants(Request $request, User $instructor): RedirectResponse
    {
        abort_unless($instructor->isInstructor() || $instructor->isTeacher(), 404);

        $serviceKeys = array_keys(config('tadris_services.grantable_services', []));

        $data = $request->validate([
            'instructor_grants_enabled' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'course_ids' => ['nullable', 'array'],
            'course_ids.*' => ['integer', 'exists:advanced_courses,id'],
            'path_ids' => ['nullable', 'array'],
            'path_ids.*' => ['integer', 'exists:learning_paths,id'],
            'service_keys' => ['nullable', 'array'],
            'service_keys.*' => ['string', Rule::in($serviceKeys)],
        ]);

        $enabled = $request->boolean('instructor_grants_enabled');
        $active = $request->boolean('is_active');
        $courseIds = collect($data['course_ids'] ?? [])->map(fn ($id) => (int) $id)->unique()->values()->all();
        $pathIds = collect($data['path_ids'] ?? [])->map(fn ($id) => (int) $id)->unique()->values()->all();
        $selectedServices = collect($data['service_keys'] ?? [])->unique()->values()->all();
        $adminId = $request->user()?->id;

        // Auto-include learning_paths / courses services when items are granted
        if ($pathIds !== [] && ! in_array('learning_paths', $selectedServices, true)) {
            $selectedServices[] = 'learning_paths';
        }
        if ($courseIds !== [] && ! in_array('courses', $selectedServices, true)) {
            $selectedServices[] = 'courses';
        }

        DB::transaction(function () use ($instructor, $enabled, $active, $courseIds, $pathIds, $selectedServices, $adminId, $serviceKeys) {
            $instructor->forceFill([
                'is_active' => $active,
                'instructor_grants_enabled' => $enabled,
            ])->save();

            if (Schema::hasTable('instructor_course_assignments')) {
                InstructorCourseAssignment::query()
                    ->where('user_id', $instructor->id)
                    ->whereNotIn('advanced_course_id', $courseIds ?: [0])
                    ->update(['is_active' => false]);

                foreach ($courseIds as $courseId) {
                    InstructorCourseAssignment::query()->updateOrCreate(
                        [
                            'user_id' => $instructor->id,
                            'advanced_course_id' => $courseId,
                        ],
                        [
                            'is_active' => true,
                            'assigned_by' => $adminId,
                        ]
                    );

                    AdvancedCourse::query()
                        ->where('id', $courseId)
                        ->whereNull('instructor_id')
                        ->update(['instructor_id' => $instructor->id]);
                }
            }

            if (Schema::hasTable('instructor_learning_path_assignments')) {
                InstructorLearningPathAssignment::query()
                    ->where('user_id', $instructor->id)
                    ->whereNotIn('learning_path_id', $pathIds ?: [0])
                    ->update(['is_active' => false]);

                foreach ($pathIds as $pathId) {
                    InstructorLearningPathAssignment::query()->updateOrCreate(
                        [
                            'user_id' => $instructor->id,
                            'learning_path_id' => $pathId,
                        ],
                        [
                            'is_active' => true,
                            'assigned_by' => $adminId,
                        ]
                    );

                    LearningPath::query()
                        ->where('id', $pathId)
                        ->whereNull('instructor_id')
                        ->update(['instructor_id' => $instructor->id]);
                }
            }

            if (Schema::hasTable('instructor_service_assignments')) {
                InstructorServiceAssignment::query()
                    ->where('user_id', $instructor->id)
                    ->whereNotIn('service_key', $selectedServices ?: ['__none__'])
                    ->update(['is_active' => false]);

                foreach ($selectedServices as $key) {
                    if (! in_array($key, $serviceKeys, true)) {
                        continue;
                    }
                    InstructorServiceAssignment::query()->updateOrCreate(
                        [
                            'user_id' => $instructor->id,
                            'service_key' => $key,
                        ],
                        [
                            'is_active' => true,
                            'assigned_by' => $adminId,
                        ]
                    );
                }
            }
        });

        return redirect()
            ->route('admin.academy-instructors.show', $instructor)
            ->with('success', sprintf(
                'تم تحديث الصلاحيات: %d مسار مسند · %d كورس مسجّل مسند · %d خدمة. المدرب يرى توصيف المسارات ومنهج الكورسات المسندة فقط.',
                count($pathIds),
                count($courseIds),
                count($selectedServices)
            ));
    }

    public function storeAssignment(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'student_id' => 'required|exists:users,id',
            'instructor_id' => 'required|exists:users,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'scope' => ['required', Rule::in([
                StudentInstructorAssignment::SCOPE_GENERAL,
                StudentInstructorAssignment::SCOPE_COLLECTIVE,
                StudentInstructorAssignment::SCOPE_INDIVIDUAL,
                StudentInstructorAssignment::SCOPE_COURSES,
            ])],
            'notes' => 'nullable|string|max:2000',
            'starts_at' => 'nullable|date',
        ]);

        $student = User::findOrFail($data['student_id']);
        $instructor = User::findOrFail($data['instructor_id']);

        if ($student->role !== 'student') {
            return back()->withInput()->withErrors(['student_id' => 'المستخدم المحدد ليس معلمًا (متعلّم مهني).']);
        }
        if (! $instructor->isInstructor() && ! $instructor->isTeacher()) {
            return back()->withInput()->withErrors(['instructor_id' => 'المستخدم المحدد ليس مدربًا.']);
        }

        StudentInstructorAssignment::updateOrCreate(
            [
                'student_id' => $data['student_id'],
                'instructor_id' => $data['instructor_id'],
                'scope' => $data['scope'],
            ],
            [
                'academic_year_id' => $data['academic_year_id'] ?? null,
                'status' => StudentInstructorAssignment::STATUS_ACTIVE,
                'notes' => $data['notes'] ?? null,
                'assigned_by' => $request->user()?->id,
                'starts_at' => $data['starts_at'] ?? now(),
                'ends_at' => null,
            ]
        );

        return redirect()
            ->route('admin.academy-instructors.show', $instructor)
            ->with('success', 'تم توصيف المدرّب للطالب بنجاح.');
    }

    public function updateAssignmentStatus(Request $request, StudentInstructorAssignment $assignment): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in([
                StudentInstructorAssignment::STATUS_ACTIVE,
                StudentInstructorAssignment::STATUS_PAUSED,
                StudentInstructorAssignment::STATUS_ENDED,
            ])],
        ]);

        $assignment->update([
            'status' => $data['status'],
            'ends_at' => $data['status'] === StudentInstructorAssignment::STATUS_ENDED ? now() : $assignment->ends_at,
        ]);

        return back()->with('success', 'تم تحديث حالة التوصيف.');
    }

    public function destroyAssignment(StudentInstructorAssignment $assignment): RedirectResponse
    {
        $instructorId = $assignment->instructor_id;
        $assignment->delete();

        return redirect()
            ->route('admin.academy-instructors.show', $instructorId)
            ->with('success', 'تم حذف التوصيف.');
    }
}
