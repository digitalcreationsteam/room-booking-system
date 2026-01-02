<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
     public function search(Request $request)
    {
        $term = $request->get('q');

        if (!$term || strlen($term) < 2) {
            return response()->json([]);
        }

        $customers = Customer::where('customer_mobile', 'like', "%$term%")
            ->orWhere('customer_name', 'like', "%$term%")
            ->limit(10)
            ->get();

        return response()->json($customers);
    }
}
