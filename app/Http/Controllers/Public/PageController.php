<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\FAQ;
use App\Models\SiteTestimonial;
use App\Support\PlatformFaqDefaults;
use Illuminate\Http\Request;

class PageController extends Controller
{
    // Home page is handled by welcome.blade.php route

    public function about()
    {
        return redirect()->route('public.about');
    }

    public function faq()
    {
        $faqs = FAQ::active()
            ->orderBy('order')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('category');

        $categories = FAQ::active()
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values();

        // عند وجود أسئلة في لوحة التحكم نعرضها فقط؛ الأسئلة الافتراضية تظهر عند عدم وجود أي سجل نشط
        $defaultFaqs = FAQ::active()->doesntExist()
            ? PlatformFaqDefaults::items()
            : [];

        return view('public.faq', compact('faqs', 'categories', 'defaultFaqs'));
    }

    public function terms()
    {
        return view('public.terms');
    }

    public function privacy()
    {
        return view('public.privacy');
    }

    public function pricing()
    {
        $packages = \App\Models\Package::active()
            ->orderBy('order')
            ->orderByDesc('is_popular')
            ->orderByDesc('is_featured')
            ->orderBy('price')
            ->get();

        return view('public.pricing', compact('packages'));
    }

    public function team()
    {
        return view('public.team');
    }

    public function certificates()
    {
        return view('public.certificates');
    }

    public function help()
    {
        return view('public.help');
    }

    public function path()
    {
        return view('public.path', [
            'laslesNavActive' => 'path',
            'pageTitle' => __('landing.path.meta_title'),
            'pageDescription' => __('landing.path.meta_description'),
            'bodyClass' => 'lasles-path-page',
        ]);
    }

    public function refund()
    {
        return view('public.refund');
    }

    public function testimonials()
    {
        $testimonials = SiteTestimonial::query()
            ->active()
            ->ordered()
            ->get();

        return view('public.testimonials', compact('testimonials'));
    }

    public function events()
    {
        return view('public.events');
    }

    public function partners()
    {
        return view('public.partners');
    }
}
