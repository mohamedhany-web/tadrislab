<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LearningPath;
use App\Models\LearningPathLesson;
use App\Models\LearningPathPractice;
use App\Models\TeacherPathEnrollment;
use App\Models\TeacherPathProgressItem;
use App\Services\LearningPathAccessService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LearningPathController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $paths = $user->accessibleLearningPaths()
            ->withCount(['units' => fn ($q) => $q->where('is_active', true)])
            ->get();

        $enrollments = TeacherPathEnrollment::query()
            ->where('user_id', $user->id)
            ->activeAccessible()
            ->with('package:id,name')
            ->get()
            ->keyBy('learning_path_id');

        return view('student.learning-paths.index', compact('paths', 'enrollments'));
    }

    public function show(Request $request, string $slug): View
    {
        $user = $request->user();
        $path = LearningPath::query()->where('slug', $slug)->firstOrFail();

        abort_unless(LearningPathAccessService::userHasAccess($user, (int) $path->id), 403);

        $path->load([
            'units' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
            'units.lessons' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
            'units.practices' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
            'teacherTools' => fn ($q) => $q->where('teacher_tools.is_active', true),
        ]);

        $enrollment = TeacherPathEnrollment::query()
            ->where('user_id', $user->id)
            ->where('learning_path_id', $path->id)
            ->with('package:id,name')
            ->first();

        $completedKeys = $this->completedKeysFor($user->id, (int) $path->id);

        return view('student.learning-paths.show', compact('path', 'enrollment', 'completedKeys'));
    }

    public function showLesson(Request $request, string $slug, LearningPathLesson $lesson): View
    {
        [$user, $path] = $this->authorizePathItem($request, $slug);
        $lesson->loadMissing('unit');
        abort_unless((int) ($lesson->unit?->learning_path_id) === (int) $path->id, 404);
        abort_unless($lesson->is_active, 404);

        $lesson->load('unit');
        $done = $this->isItemDone($user->id, (int) $path->id, TeacherPathProgressItem::TYPE_LESSON, (int) $lesson->id);
        $enrollment = TeacherPathEnrollment::query()
            ->where('user_id', $user->id)
            ->where('learning_path_id', $path->id)
            ->first();

        return view('student.learning-paths.lesson', compact('path', 'lesson', 'done', 'enrollment'));
    }

    public function showPractice(Request $request, string $slug, LearningPathPractice $practice): View
    {
        [$user, $path] = $this->authorizePathItem($request, $slug);
        $practice->loadMissing('unit');
        abort_unless((int) ($practice->unit?->learning_path_id) === (int) $path->id, 404);
        abort_unless($practice->is_active, 404);

        $practice->load('unit');
        $done = $this->isItemDone($user->id, (int) $path->id, TeacherPathProgressItem::TYPE_PRACTICE, (int) $practice->id);
        $enrollment = TeacherPathEnrollment::query()
            ->where('user_id', $user->id)
            ->where('learning_path_id', $path->id)
            ->first();

        return view('student.learning-paths.practice', compact('path', 'practice', 'done', 'enrollment'));
    }

    public function completeItem(Request $request, string $slug): RedirectResponse
    {
        $user = $request->user();
        $path = LearningPath::query()->where('slug', $slug)->firstOrFail();

        $data = $request->validate([
            'item_type' => ['required', Rule::in([TeacherPathProgressItem::TYPE_LESSON, TeacherPathProgressItem::TYPE_PRACTICE])],
            'item_id' => ['required', 'integer', 'min:1'],
        ]);

        LearningPathAccessService::markItemComplete(
            $user,
            $path,
            $data['item_type'],
            (int) $data['item_id']
        );

        return back()->with('success', 'تم تسجيل إكمال العنصر وتحديث تقدّمك كمعلم.');
    }

    /** @return array{0: \App\Models\User, 1: LearningPath} */
    private function authorizePathItem(Request $request, string $slug): array
    {
        $user = $request->user();
        $path = LearningPath::query()->where('slug', $slug)->firstOrFail();
        abort_unless(LearningPathAccessService::userHasAccess($user, (int) $path->id), 403);

        return [$user, $path];
    }

    private function completedKeysFor(int $userId, int $pathId)
    {
        return TeacherPathProgressItem::query()
            ->where('user_id', $userId)
            ->where('learning_path_id', $pathId)
            ->where('is_completed', true)
            ->get()
            ->mapWithKeys(fn (TeacherPathProgressItem $item) => [
                $item->item_type.':'.$item->item_id => true,
            ]);
    }

    private function isItemDone(int $userId, int $pathId, string $type, int $itemId): bool
    {
        return TeacherPathProgressItem::query()
            ->where('user_id', $userId)
            ->where('learning_path_id', $pathId)
            ->where('item_type', $type)
            ->where('item_id', $itemId)
            ->where('is_completed', true)
            ->exists();
    }
}
