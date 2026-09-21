<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Services\CourseSubscriptionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseShowController extends Controller
{
    public function show(Request $request, int $id): View
    {
        $course = AdvancedCourse::query()
            ->where('id', $id)
            ->where('is_active', true)
            ->with(['academicSubject', 'academicYear', 'instructor', 'courseCategory'])
            ->withCount('lessons')
            ->firstOrFail();

        $isEnrolled = auth()->check() && auth()->user()->isEnrolledIn($course->id);

        $relatedCourses = AdvancedCourse::query()
            ->where('is_active', true)
            ->where('id', '!=', $course->id)
            ->where(function ($query) use ($course) {
                $delivery = $course->delivery_type ?: CourseSubscriptionService::DELIVERY_GROUP;
                if ($delivery === CourseSubscriptionService::DELIVERY_ONE_TO_ONE) {
                    $query->where('delivery_type', CourseSubscriptionService::DELIVERY_ONE_TO_ONE);
                } else {
                    $query->where(function ($q) {
                        $q->whereNull('delivery_type')
                            ->orWhere('delivery_type', CourseSubscriptionService::DELIVERY_GROUP);
                    });
                }
                if ($course->course_category_id) {
                    $query->where(function ($q) use ($course) {
                        $q->where('course_category_id', $course->course_category_id)
                            ->orWhere('academic_subject_id', $course->academic_subject_id)
                            ->orWhere('is_featured', true);
                    });
                }
            })
            ->with(['academicSubject', 'instructor'])
            ->withCount('lessons')
            ->limit(3)
            ->get();

        $from = $request->query('from');
        if (! in_array($from, ['groups', 'one_to_one'], true)) {
            $from = $course->isOneToOne() ? 'one_to_one' : 'groups';
        }

        $isRtl = app()->getLocale() === 'ar';
        $brand = $isRtl ? 'تدريس لاب' : config('app.name', 'TADRIS LAB');
        $pageTitle = ($course->title ?? __('public.course_detail_title')).' — '.$brand;
        $pageDescription = \Illuminate\Support\Str::limit(strip_tags((string) ($course->description ?? '')), 160);
        $bodyClass = 'lasles-course-detail-page';
        $laslesNavActive = 'courses';

        return view('course-show', compact(
            'course',
            'relatedCourses',
            'isEnrolled',
            'from',
            'pageTitle',
            'pageDescription',
            'bodyClass',
            'laslesNavActive'
        ));
    }
}
