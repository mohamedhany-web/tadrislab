<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\LearningPath;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LearningPathController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $ids = $user->teachingLearningPathIds();

        $paths = LearningPath::query()
            ->whereIn('id', $ids->all() ?: [0])
            ->withCount(['units', 'lessons', 'practices', 'enrollments'])
            ->ordered()
            ->get();

        return view('instructor.learning-paths.index', compact('paths'));
    }

    public function show(Request $request, LearningPath $learningPath): View
    {
        $user = $request->user();
        abort_unless($user->teachingLearningPathIds()->contains((int) $learningPath->id), 403);

        $learningPath->load([
            'units.lessons',
            'units.practices',
            'enrollments' => fn ($q) => $q->activeAccessible()->with('user:id,name,email')->latest('enrolled_at')->limit(50),
        ]);

        return view('instructor.learning-paths.show', ['path' => $learningPath]);
    }

    /**
     * تحديث التوصيف الظاهر للمتعلم فقط (بدون وحدات/دروس/ممارسات).
     */
    public function updateDescription(Request $request, LearningPath $learningPath): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->teachingLearningPathIds()->contains((int) $learningPath->id), 403);

        $data = $request->validate([
            'summary_ar' => ['nullable', 'string', 'max:500'],
            'summary_en' => ['nullable', 'string', 'max:500'],
            'description_ar' => ['nullable', 'string', 'max:10000'],
            'description_en' => ['nullable', 'string', 'max:10000'],
            'skill_focus_ar' => ['nullable', 'string', 'max:255'],
            'skill_focus_en' => ['nullable', 'string', 'max:255'],
            'estimated_minutes' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'thumbnail' => ['nullable', 'image', 'max:4096'],
        ]);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('learning-paths', 'public');
        } else {
            unset($data['thumbnail']);
        }

        $learningPath->fill($data)->save();

        return redirect()
            ->route('instructor.learning-paths.show', $learningPath)
            ->with('success', app()->getLocale() === 'ar'
                ? 'تم تحديث توصيف المسار — يظهر فورًا للمتعلمين في الصفحة العامة.'
                : 'Path description updated — visible immediately on the public page.');
    }
}
