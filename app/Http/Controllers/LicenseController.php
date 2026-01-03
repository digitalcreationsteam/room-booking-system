<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hotel;
use Illuminate\Support\Facades\DB;

class LicenseController extends Controller
{
    /**
     * Generate Machine ID (Client Side)
     */
    public static function getMachineId()
    {
        return substr(md5(
            php_uname() .
            disk_total_space('/') .
            gethostname()
        ), 0, 12);
    }

    /**
     * API: Get Machine ID for client
     * Route: GET /api/get-machine-id
     */
    public function getClientMachineId()
    {
        return response()->json([
            'success' => true,
            'machine_id' => self::getMachineId()
        ]);
    }
}
