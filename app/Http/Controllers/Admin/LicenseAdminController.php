<?php
// ============================================
// ADMIN CONTROLLER FOR LICENSE GENERATION
// ============================================

// File: app/Http/Controllers/Admin/LicenseAdminController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LicenseAdminController extends Controller
{
    /**
     * Show License Generator Form
     * Route: GET /admin/license/generate
     */
    public function showGenerateForm(): View
    {
        return view('admin.license-generate');
    }

    /**
     * Generate License Key
     * Route: POST /admin/license/generate
     */
    public function generateLicense(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'expiry' => 'required|date|after:today',
            'machine_id' => 'required|string|size:12'
        ]);

        $licenseData = [
            'email' => $request->email,
            'expiry' => $request->expiry,
            'machine_id' => $request->machine_id
        ];

        // Generate license key
        $licenseKey = base64_encode(json_encode($licenseData));

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'license_key' => $licenseKey,
                'data' => $licenseData
            ]);
        }

        return back()->with([
            'success' => 'License key generated successfully!',
            'license_key' => $licenseKey,
            'license_data' => $licenseData
        ]);
    }
}
