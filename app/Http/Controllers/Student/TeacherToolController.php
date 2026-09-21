<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\TeacherTool;
use App\Services\TeacherToolAccessService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TeacherToolController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $tools = TeacherTool::query()
            ->published()
            ->ordered()
            ->with(['learningPaths:id,title_ar,slug', 'packages:id,name,slug'])
            ->get()
            ->filter(fn (TeacherTool $tool) => TeacherToolAccessService::canAccess($user, $tool))
            ->values();

        return view('student.tools.index', [
            'tools' => $tools,
            'typeLabels' => TeacherTool::typeLabels(),
        ]);
    }
}
