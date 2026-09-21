<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\TeacherTool;
use App\Services\TeacherToolAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TeacherToolController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->string('type')->toString() ?: null;

        $tools = TeacherTool::query()
            ->published()
            ->ofType($type)
            ->ordered()
            ->get();

        return view('public.tools.index', [
            'tools' => $tools,
            'typeLabels' => TeacherTool::typeLabels(),
            'activeType' => $type,
            'laslesNavActive' => 'resources',
            'pageTitle' => __('landing.tools.meta_title'),
            'pageDescription' => __('landing.tools.meta_description'),
            'bodyClass' => 'lasles-tools-page',
        ]);
    }

    public function show(string $slug): View
    {
        $tool = TeacherTool::query()
            ->published()
            ->where('slug', $slug)
            ->with(['learningPaths' => fn ($q) => $q->published(), 'packages' => fn ($q) => $q->where('is_active', true)])
            ->firstOrFail();

        $canAccess = TeacherToolAccessService::canAccess(Auth::user(), $tool);

        return view('public.tools.show', [
            'tool' => $tool,
            'canAccess' => $canAccess,
            'typeLabels' => TeacherTool::typeLabels(),
            'laslesNavActive' => 'resources',
            'pageTitle' => $tool->title().' — '.__('common.app_name'),
            'pageDescription' => $tool->summary() ?: __('landing.tools.meta_description'),
            'bodyClass' => 'lasles-tools-page lasles-tools-page--show',
        ]);
    }

    public function download(string $slug): StreamedResponse|\Illuminate\Http\RedirectResponse
    {
        $tool = TeacherTool::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        if (! TeacherToolAccessService::canAccess(Auth::user(), $tool)) {
            return redirect()
                ->route('public.tools.show', $tool->slug)
                ->with('error', __('landing.tools.access_denied'));
        }

        if (! $tool->hasDownloadableFile() || ! Storage::disk('public')->exists($tool->file_path)) {
            if ($tool->hasExternalLink()) {
                return redirect()->away($tool->external_url);
            }

            return redirect()
                ->route('public.tools.show', $tool->slug)
                ->with('error', __('landing.tools.no_file'));
        }

        return Storage::disk('public')->download(
            $tool->file_path,
            $tool->file_name ?: basename($tool->file_path)
        );
    }
}
