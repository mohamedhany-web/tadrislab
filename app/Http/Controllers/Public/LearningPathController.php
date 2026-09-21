<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\LearningPath;
use Illuminate\View\View;

class LearningPathController extends Controller
{
    public function index(): View
    {
        $paths = LearningPath::query()
            ->published()
            ->withCount(['units' => fn ($q) => $q->where('is_active', true)])
            ->ordered()
            ->get();

        return view('public.learning-paths.index', [
            'paths' => $paths,
            'laslesNavActive' => 'teacher-paths',
            'pageTitle' => __('landing.learning_paths.meta_title'),
            'pageDescription' => __('landing.learning_paths.meta_description'),
            'bodyClass' => 'lasles-paths-page',
        ]);
    }

    public function show(string $slug): View
    {
        $path = LearningPath::query()
            ->published()
            ->where('slug', $slug)
            ->with([
                'units' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
                'units.lessons' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
                'units.practices' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
            ])
            ->firstOrFail();

        return view('public.learning-paths.show', [
            'path' => $path,
            'relatedPaths' => LearningPath::query()
                ->published()
                ->where('id', '!=', $path->id)
                ->withCount(['units' => fn ($q) => $q->where('is_active', true)])
                ->ordered()
                ->limit(3)
                ->get(),
            'laslesNavActive' => 'teacher-paths',
            'pageTitle' => $path->title().' — '.__('common.app_name'),
            'pageDescription' => $path->summary() ?: __('landing.learning_paths.meta_description'),
            'bodyClass' => 'lasles-paths-page lasles-path-detail-page',
        ]);
    }
}
