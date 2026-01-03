<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Hotel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user'  => $request->user(),
            'hotel' => $request->user()->hotel
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email',

            'hotel_name'        => 'required|string|max:255',
            'hotel_address'     => 'nullable|string',
            'hotel_mobile'      => 'nullable|string|max:10',
            'hotel_telephone'   => 'nullable|string',
            'hotel_l_t_number'  => 'nullable|string',
            'hotel_gst_number'  => 'nullable|string',
            'hotel_email'       => 'nullable|email',
        ]);

        /* ================= USER UPDATE ================= */
        $user = $request->user();
        $user->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
        ]);

        /* ================= HOTEL UPDATE ================= */
        $hotel = $user->hotel()->firstOrCreate(
            ['user_id' => $user->id],
            ['hotel_name' => $validated['hotel_name']]
        );

        $hotel->update([
            'hotel_name'       => $validated['hotel_name'],
            'hotel_address'    => $validated['hotel_address'] ?? null,
            'hotel_mobile'     => $validated['hotel_mobile'] ?? null,
            'hotel_telephone'  => $validated['hotel_telephone'] ?? null,
            'hotel_l_t_number' => $validated['hotel_l_t_number'] ?? null,
            'hotel_gst_number' => $validated['hotel_gst_number'] ?? null,
            'hotel_email'      => $validated['hotel_email'] ?? null,
        ]);

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        Auth::logout();
        $request->user()->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    // ============================================
    // LICENSE MANAGEMENT METHODS
    // ============================================

    /**
     * Get Machine ID for License Generation
     * Route: GET /profile/get-machine-id
     */
    public function getMachineId()
    {
        $machineId = $this->generateMachineId();

        return response()->json([
            'success' => true,
            'machine_id' => $machineId,
            'message' => 'Copy this Machine ID and send to admin for license generation'
        ]);
    }

    /**
     * Show License Management Page
     * Route: GET /profile/license
     */
    public function showLicense(Request $request): View
    {
        $hotel = $request->user()->hotel;

        $licenseInfo = null;
        $daysRemaining = null;

        if ($hotel && $hotel->license_key) {
            $licenseData = json_decode(base64_decode($hotel->license_key), true);

            if ($licenseData) {
                $expiry = strtotime($licenseData['expiry']);
                $today = time();
                $daysRemaining = ceil(($expiry - $today) / 86400);

                $licenseInfo = [
                    'email' => $licenseData['email'],
                    'expiry' => $licenseData['expiry'],
                    'machine_id' => $licenseData['machine_id'],
                    'days_remaining' => $daysRemaining,
                    'is_expired' => $daysRemaining < 0,
                    'is_expiring_soon' => $daysRemaining <= 7 && $daysRemaining >= 0
                ];
            }
        }

        return view('profile.license', [
            'user' => $request->user(),
            'hotel' => $hotel,
            'machine_id' => $this->generateMachineId(),
            'license_info' => $licenseInfo
        ]);
    }

    /**
     * Activate or Update License Key
     * Route: POST /profile/license/activate
     */
    public function activateLicense(Request $request): RedirectResponse
    {
        $request->validate([
            'license_key' => 'required|string'
        ]);

        $user = $request->user();
        $hotel = $user->hotel;

        if (!$hotel) {
            return redirect()
                ->back()
                ->with('error', 'Please create hotel profile first');
        }

        // Validate license key
        $validation = $this->validateLicenseKey($request->license_key);

        if (!$validation['valid']) {
            return redirect()
                ->back()
                ->with('error', $validation['message']);
        }

        // Update license in hotel
        $hotel->update([
            'license_key' => $request->license_key,
            'license_expiry' => $validation['expiry']
        ]);

        return redirect()
            ->route('profile.license')
            ->with('success', 'License activated successfully! Valid until ' . $validation['expiry']);
    }

    /**
     * Renew License (same as activate, different message)
     * Route: POST /profile/license/renew
     */
    public function renewLicense(Request $request): RedirectResponse
    {
        $request->validate([
            'license_key' => 'required|string'
        ]);

        $user = $request->user();
        $hotel = $user->hotel;

        if (!$hotel) {
            return redirect()
                ->back()
                ->with('error', 'Hotel profile not found');
        }

        // Validate new license key
        $validation = $this->validateLicenseKey($request->license_key);

        if (!$validation['valid']) {
            return redirect()
                ->back()
                ->with('error', $validation['message']);
        }

        // Update license
        $hotel->update([
            'license_key' => $request->license_key,
            'license_expiry' => $validation['expiry']
        ]);

        return redirect()
            ->route('profile.license')
            ->with('success', 'License renewed successfully! New expiry date: ' . $validation['expiry']);
    }

    // ============================================
    // HELPER METHODS
    // ============================================

    /**
     * Generate Machine ID
     */
    private function generateMachineId(): string
    {
        return substr(md5(
            php_uname() .
            disk_total_space('/') .
            gethostname()
        ), 0, 12);
    }

    /**
     * Validate License Key
     */
    private function validateLicenseKey(string $licenseKey): array
    {
        // Decode license
        $licenseData = json_decode(base64_decode($licenseKey), true);

        if (!$licenseData) {
            return [
                'valid' => false,
                'message' => 'Invalid license key format'
            ];
        }

        // Check required fields
        if (!isset($licenseData['machine_id']) || !isset($licenseData['expiry']) || !isset($licenseData['email'])) {
            return [
                'valid' => false,
                'message' => 'License key is incomplete or corrupted'
            ];
        }

        // Check machine ID match
        $currentMachineId = $this->generateMachineId();
        if ($licenseData['machine_id'] !== $currentMachineId) {
            return [
                'valid' => false,
                'message' => 'This license key is not valid for this machine. Machine ID mismatch.'
            ];
        }

        // Check expiry date
        if (strtotime($licenseData['expiry']) < time()) {
            return [
                'valid' => false,
                'message' => 'License key has expired. Please contact admin for renewal.'
            ];
        }

        return [
            'valid' => true,
            'expiry' => $licenseData['expiry'],
            'email' => $licenseData['email']
        ];
    }
}

