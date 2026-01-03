<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminLicenseController extends Controller
{
    /**
     * Generate License Key (Admin Side)
     * Route: POST /api/admin/generate-license
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

        // Generate license key (Base64 encoded JSON)
        $licenseKey = base64_encode(json_encode($licenseData));

        return response()->json([
            'success' => true,
            'license_key' => $licenseKey,
            'data' => $licenseData
        ]);
    }
}
