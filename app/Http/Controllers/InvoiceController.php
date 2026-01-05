<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function edit(Request $request): View
    {
        return view('invoice.edit', [
            'user'  => $request->user(),
            'hotel' => $request->user()->hotel
        ]);
    }
}