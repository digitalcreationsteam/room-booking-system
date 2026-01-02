@extends('layouts.app')

@section('title', 'Tax Report')
@section('header', 'Tax Report (For CA)')

@section('content')
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">From Date</label>
            <input type="date" name="from_date" value="{{ $fromDate->format('Y-m-d') }}"
                class="w-full px-3 py-2 border rounded">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">To Date</label>
            <input type="date" name="to_date" value="{{ $toDate->format('Y-m-d') }}"
                class="w-full px-3 py-2 border rounded">
        </div>
        <div class="flex items-end">
            <button type="submit" class="w-full bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                <i class="fas fa-search mr-2"></i> Filter
            </button>
        </div>
    </form>
</div>

<!-- Tax Summary -->
<!-- Tax Summary Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mb-6">

    <div class="bg-blue-50 rounded-lg p-6 border border-blue-200">
        <p class="text-sm text-gray-600">Room Charges (Taxable)</p>
        <p class="text-3xl font-bold text-blue-600">
            ₹{{ number_format($taxSummary['total_room_charges'], 2) }}
        </p>
    </div>

    <div class="bg-green-50 rounded-lg p-6 border border-green-200">
        <p class="text-sm text-gray-600">GST Collected</p>
        <p class="text-3xl font-bold text-green-600">
            ₹{{ number_format($taxSummary['total_gst'], 2) }}
        </p>
    </div>

    <div class="bg-orange-50 rounded-lg p-6 border border-orange-200">
        <p class="text-sm text-gray-600">Service Tax</p>
        <p class="text-3xl font-bold text-orange-600">
            ₹{{ number_format($taxSummary['total_service_tax'], 2) }}
        </p>
    </div>

    <div class="bg-purple-50 rounded-lg p-6 border border-purple-200">
        <p class="text-sm text-gray-600">Other Charges</p>
        <p class="text-3xl font-bold text-purple-600">
            ₹{{ number_format($taxSummary['total_other_charges'], 2) }}
        </p>
    </div>

    <div class="bg-yellow-50 rounded-lg p-6 border border-yellow-200">
        <p class="text-sm text-gray-600">Extra Charges</p>
        <p class="text-3xl font-bold text-yellow-600">
            ₹{{ number_format($taxSummary['total_extra_charges'], 2) }}
        </p>
    </div>

    <div class="bg-red-50 rounded-lg p-6 border border-red-200">
        <p class="text-sm text-gray-600">Grand Total</p>
        <p class="text-3xl font-bold text-red-600">
            ₹{{ number_format($taxSummary['grand_total'], 2) }}
        </p>
    </div>

</div>


<!-- GST Breakdown -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="text-lg font-semibold mb-4">GST Breakdown by Rate</h3>
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left">GST Rate</th>
                <th class="px-6 py-3 text-left">Bookings Count</th>
                <th class="px-6 py-3 text-left">Taxable Amount</th>
                <th class="px-6 py-3 text-left">GST Amount</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @foreach($gstBreakdown as $gst)
                <tr>
                    <td class="px-6 py-4 font-semibold">{{ $gst['gst_rate'] }}%</td>
                    <td class="px-6 py-4">{{ $gst['bookings_count'] }}</td>
                    <td class="px-6 py-4">₹{{ number_format($gst['taxable_amount'], 2) }}</td>
                    <td class="px-6 py-4 text-green-600 font-semibold">₹{{ number_format($gst['gst_amount'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Detailed Bookings -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b">
        <h3 class="text-lg font-semibold">Booking-wise Tax Details</h3>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left">Booking #</th>
                <th class="px-4 py-3 text-left">Customer/Company</th>
                <th class="px-4 py-3 text-left">GST Number</th>
                <th class="px-4 py-3 text-left">Taxable</th>
                <th class="px-4 py-3 text-left">GST</th>
                <th class="px-4 py-3 text-left">Total</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @foreach($bookings as $booking)
                <tr>
                    <td class="px-4 py-3">{{ $booking->booking_number }}</td>
                    <td class="px-4 py-3">
                        {{ $booking->customer_name }}<br>
                        @if($booking->company_name)
                            <span class="text-xs text-gray-500">{{ $booking->company_name }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">{{ $booking->gst_number ?? 'N/A' }}</td>
                    <td class="px-4 py-3">₹{{ number_format($booking->room_charges, 2) }}</td>
                    <td class="px-4 py-3 text-green-600">₹{{ number_format($booking->gst_amount, 2) }}</td>
                    <td class="px-4 py-3 font-semibold">₹{{ number_format($booking->total_amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
