<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\LearningPath;
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
}
