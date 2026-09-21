<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\UserPackageEntitlement;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * باقات المعلم المفعّلة (منسّق الوصول: مسارات + أدوات + جلسات استشارة).
 */
class PackageEntitlementController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $entitlements = UserPackageEntitlement::query()
            ->where('user_id', $user->id)
            ->with(['package'])
            ->latest('activated_at')
            ->get();

        $active = $entitlements->filter(fn (UserPackageEntitlement $e) => $e->isActive());

        return view('student.packages.index', [
            'entitlements' => $entitlements,
            'active' => $active,
        ]);
    }
}
