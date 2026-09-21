<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\TeacherAssistant\TeacherAssistantService;
use App\Support\PlatformModules;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeacherAssistantController extends Controller
{
    public function __construct(
        private readonly TeacherAssistantService $assistant,
    ) {}

    public function index(Request $request): View
    {
        abort_unless(PlatformModules::enabled('teacher_assistant'), 404);

        return view('student.teacher-assistant.index', [
            'result' => null,
            'labels' => TeacherAssistantService::outputLabels(),
            'provider' => $this->assistant->resolveProvider()->name(),
        ]);
    }

    public function generate(Request $request): View
    {
        abort_unless(PlatformModules::enabled('teacher_assistant'), 404);

        $data = $request->validate([
            'lesson_name' => ['required', 'string', 'max:200'],
            'subject' => ['nullable', 'string', 'max:120'],
            'grade' => ['nullable', 'string', 'max:80'],
        ], [
            'lesson_name.required' => 'اسم الدرس / موضوع الممارسة مطلوب.',
        ]);

        $result = $this->assistant->generate([
            'lesson_name' => $data['lesson_name'],
            'subject' => $data['subject'] ?? null,
            'grade' => $data['grade'] ?? null,
            'locale' => app()->getLocale(),
        ]);

        return view('student.teacher-assistant.index', [
            'result' => $result,
            'labels' => TeacherAssistantService::outputLabels(),
            'provider' => $result['meta']['provider'] ?? $this->assistant->resolveProvider()->name(),
            'input' => $data,
        ]);
    }
}
