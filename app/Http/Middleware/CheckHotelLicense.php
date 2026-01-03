<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class CheckHotelLicense
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $hotel = $user->hotel;

        // Check if hotel exists
        if (!$hotel) {
            return redirect()
                ->route('profile.edit')
                ->with('error', 'Please complete your hotel profile first');
        }

        // Check if license exists
        if (!$hotel->license_key || !$hotel->license_expiry) {
            return redirect()
                ->route('profile.license')
                ->with('error', 'Please activate your license to access the system');
        }

        // Check expiry from database column (OPTIMIZED)
        $expiry = Carbon::parse($hotel->license_expiry);
        $today = Carbon::today();

        if ($expiry->lt($today)) {
            // License expired
            return redirect()
                ->route('profile.license')
                ->with('error', 'Your license has expired on ' . $expiry->format('d M Y') . '. Please renew to continue.');
        }

        // Check if expiring soon (7 days warning)
        $daysLeft = $today->diffInDays($expiry, false);

        if ($daysLeft <= 7 && $daysLeft >= 0) {
            session()->flash('warning', "⚠️ Your license will expire in $daysLeft days. Please renew soon.");
        }

        // Optional: Verify machine ID (only if needed for extra security)
        if (config('license.verify_machine_id', true)) {
            $licenseData = json_decode(base64_decode($hotel->license_key), true);

            if ($licenseData && isset($licenseData['machine_id'])) {
                $currentMachineId = $this->getMachineId();

                if ($licenseData['machine_id'] !== $currentMachineId) {
                    return redirect()
                        ->route('profile.license')
                        ->with('error', 'License not valid for this machine. Please contact admin.');
                }
            }
        }

        return $next($request);
    }

    /**
     * Generate Machine ID
     */
    private function getMachineId(): string
    {
        return substr(md5(
            php_uname() .
            disk_total_space('/') .
            gethostname()
        ), 0, 12);
    }
}
