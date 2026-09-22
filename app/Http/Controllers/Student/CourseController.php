<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CourseController extends Controller
{
    /**
     * عرض تفاصيل الكورس
     */
    public function show(AdvancedCourse $advancedCourse)
    {
        $advancedCourse->load(['academicYear', 'academicSubject']);
        
        // التحقق من وجود طلب سابق للطالب
        $existingOrder = Order::where('user_id', auth()->id())
            ->where('advanced_course_id', $advancedCourse->id)
            ->latest()
            ->first();

        // التحقق من التسجيل في الكورس (يشمل انتهاء الاشتراك الشهري)
        $isEnrolled = auth()->check() && auth()->user()->isEnrolledIn($advancedCourse->id);

        // حسابات التحويل اليدوي للمنصة
        $availableWallets = \App\Services\PlatformPaymentAccountService::activeAccounts();

        return view('student.courses.show', compact('advancedCourse', 'existingOrder', 'isEnrolled', 'availableWallets'));
    }
}