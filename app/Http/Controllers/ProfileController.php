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

    // public function update(Request $request)
    // {
    //     $user = $request->user();

    //     // ================= USER VALIDATION =================
    //     $request->validate([
    //         'name'  => 'required|string|max:255',
    //         'email' => 'required|email|max:255',
    //     ]);

    //     $user->update([
    //         'name'  => $request->name,
    //         'email' => $request->email,
    //     ]);

    //     // ================= HOTEL VALIDATION =================
    //     $request->validate([
    //         'hotel_name'       => 'required|string|max:255',
    //         'hotel_address'    => 'nullable|string',
    //         'hotel_gst_number' => 'nullable|string|max:20',
    //         'hotel_mobile'     => 'nullable|string|max:15',
    //         'hotel_telephone'  => 'nullable|string|max:15',
    //         'hotel_email'      => 'nullable|email',
    //     ]);

    //     Hotel::updateOrCreate(
    //         ['user_id' => $user->id],
    //         [
    //             'hotel_name'       => $request->hotel_name,
    //             'hotel_address'    => $request->hotel_address,
    //             'hotel_gst_number' => $request->hotel_gst_number,
    //             'hotel_mobile'     => $request->hotel_mobile,
    //             'hotel_telephone'  => $request->hotel_telephone,
    //             'hotel_email'      => $request->hotel_email,
    //         ]
    //     );

    //     return Redirect::route('profile.edit')
    //         ->with('success', 'Profile & Hotel details updated successfully');
    // }

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
}
