<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\CourseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class CoursesController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $categoryId = (int) $request->query('category', 0);
        $level = trim((string) $request->query('level', ''));
        $featuredOnly = $request->boolean('featured');

        $levels = AdvancedCourse::query()
            ->where('is_active', true)
            ->whereNotNull('level')
            ->where('level', '!=', '')
            ->distinct()
            ->orderBy('level')
            ->pluck('level')
            ->filter()
            ->values();

        $categories = collect();
        if (Schema::hasTable('course_categories')) {
            $categories = CourseCategory::query()
                ->active()
                ->ordered()
                ->withCount(['advancedCourses as courses_count' => fn ($query) => $query->where('is_active', true)])
                ->having('courses_count', '>', 0)
                ->get();
        }

        $coursesQuery = AdvancedCourse::query()
            ->where('is_active', true)
            ->with(['instructor:id,name', 'courseCategory:id,name', 'academicSubject:id,name'])
            ->withCount('lessons')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('title', 'like', '%'.$q.'%')
                        ->orWhere('description', 'like', '%'.$q.'%')
                        ->orWhere('category', 'like', '%'.$q.'%');
                });
            })
            ->when($categoryId > 0, fn ($query) => $query->where('course_category_id', $categoryId))
            ->when($level !== '', fn ($query) => $query->where('level', $level))
            ->when($featuredOnly, fn ($query) => $query->where('is_featured', true))
            ->orderByDesc('is_featured')
            ->orderByDesc('created_at');

        $courses = $coursesQuery->paginate(12)->withQueryString();

        $filters = [
            'q' => $q,
            'category' => $categoryId > 0 ? $categoryId : null,
            'level' => $level !== '' ? $level : null,
            'featured' => $featuredOnly ?: null,
        ];

        return view('public.courses.index', [
            'courses' => $courses,
            'categories' => $categories,
            'levels' => $levels,
            'filters' => $filters,
            'totalActive' => AdvancedCourse::query()->where('is_active', true)->count(),
        ]);
    }
}
