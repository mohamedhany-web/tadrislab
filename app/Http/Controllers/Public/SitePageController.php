<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Support\TadrisPublicNav;
use Illuminate\Http\Request;

class SitePageController extends Controller
{
    public function show(string $page)
    {
        $node = TadrisPublicNav::find($page);
        abort_if(! $node || ! ($node['enabled'] ?? true), 404);

        $content = __('site.pages.'.$page);
        if (! is_array($content)) {
            $content = [
                'title' => $node['label'],
                'lead' => '',
                'sections' => [],
            ];
        }

        $packages = \App\Support\TadrisServicePages::packagesFor($page);
        $relatedPages = \App\Support\TadrisServicePages::relatedPages($page);
        $catalogUrl = \App\Support\TadrisServicePages::catalogUrl($page);
        $deliveryKey = \App\Support\TadrisServicePages::deliveryKey($page);

        return view($node['view'] ?? 'public.site.show', [
            'pageKey' => $page,
            'node' => $node,
            'content' => $content,
            'packages' => $packages,
            'relatedPages' => $relatedPages,
            'catalogUrl' => $catalogUrl,
            'deliveryKey' => $deliveryKey,
            'laslesNavActive' => $page,
            'pageTitle' => ($content['meta_title'] ?? null) ?: (($content['title'] ?? $content['title_before'] ?? $node['label']).' — '.__('common.app_name')),
            'pageDescription' => $content['meta_description'] ?? ($content['lead'] ?? __('landing.meta.description')),
            'bodyClass' => 'lasles-site-page lasles-site-page--'.$page,
        ]);
    }

    public function contact()
    {
        $node = TadrisPublicNav::find('contact');
        $content = __('site.pages.contact');

        return view('public.site.contact', [
            'pageKey' => 'contact',
            'node' => $node,
            'content' => is_array($content) ? $content : [],
            'laslesNavActive' => 'contact',
            'pageTitle' => ($content['meta_title'] ?? null) ?: (__('site.nav.contact').' — '.__('common.app_name')),
            'pageDescription' => $content['meta_description'] ?? ($content['lead'] ?? ''),
            'bodyClass' => 'lasles-site-page lasles-site-page--contact',
        ]);
    }

    public function contactStore(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'topic' => ['nullable', 'string', 'max:255'],
            'inquiry_type' => ['nullable', 'string', 'in:'.implode(',', \App\Models\Inquiry::typeKeys())],
            'message' => ['required', 'string', 'max:5000'],
            'order_id' => ['nullable', 'integer', 'exists:orders,id'],
            'consultation_request_id' => ['nullable', 'integer', 'exists:consultation_requests,id'],
            'institution_id' => ['nullable', 'integer', 'exists:institutions,id'],
        ]);

        $message = \App\Models\ContactMessage::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'subject' => $validated['topic'] ?: __('site.nav.contact'),
            'message' => $validated['message'],
        ]);

        try {
            \App\Services\InquiryService::fromContactForm($validated, $message);
        } catch (\Throwable $e) {
            report($e);
        }

        try {
            app(\App\Services\ContactMessageAlertService::class)->notifyAdmins($message);
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('public.contact')
            ->with('status', __('site.pages.contact.success'));
    }
}
