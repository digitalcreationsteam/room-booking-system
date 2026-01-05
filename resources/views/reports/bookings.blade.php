<!-- File: resources/views/reports/bookings.blade.php -->
@extends('layouts.app')

@section('title', 'Booking Reports')
@section('header', 'Booking Reports')

@section('content')
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">From Date</label>
            <input type="date" name="from_date" value="{{ request('from_date') }}"
                class="w-full px-3 py-2 border rounded">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">To Date</label>
            <input type="date" name="to_date" value="{{ request('to_date') }}"
                class="w-full px-3 py-2 border rounded">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Booking Status</label>
            <select name="booking_status" class="w-full px-3 py-2 border rounded">
                <option value="">All</option>
                <option value="confirmed" {{ request('booking_status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="checked_in" {{ request('booking_status') == 'checked_in' ? 'selected' : '' }}>Checked In</option>
                <option value="checked_out" {{ request('booking_status') == 'checked_out' ? 'selected' : '' }}>Checked Out</option>
                <option value="cancelled" {{ request('booking_status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Payment Status</label>
            <select name="payment_status" class="w-full px-3 py-2 border rounded">
                <option value="">All</option>
                <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                <i class="fas fa-search"></i> Filter
            </button>
            <a href="{{ route('reports.export-bookings', request()->all()) }}"
                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                <i class="fas fa-file-excel"></i>
            </a>
        </div>
    </form>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
        <p class="text-sm text-gray-600">Total Bookings</p>
        <p class="text-2xl font-bold text-blue-600">{{ $summary['total_bookings'] }}</p>
    </div>
    <div class="bg-green-50 rounded-lg p-4 border border-green-200">
        <p class="text-sm text-gray-600">Total Revenue</p>
        <p class="text-2xl font-bold text-green-600">₹{{ number_format($summary['total_revenue'], 2) }}</p>
    </div>
    <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
        <p class="text-sm text-gray-600">Advance Collected</p>
        <p class="text-2xl font-bold text-yellow-600">₹{{ number_format($summary['advance_collected'], 2) }}</p>
    </div>
    <div class="bg-red-50 rounded-lg p-4 border border-red-200">
        <p class="text-sm text-gray-600">Pending Amount</p>
        <p class="text-2xl font-bold text-red-600">₹{{ number_format($summary['pending_amount'], 2) }}</p>
    </div>
</div>

<!-- Bookings Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left">Sr No</th>
                <th class="px-4 py-3 text-left">Booking #</th>
                <th class="px-4 py-3 text-left">Customer</th>
                <th class="px-4 py-3 text-left">Company/GST</th>
                <th class="px-4 py-3 text-left">Check-in</th>
                <th class="px-4 py-3 text-left">Nights</th>
                <th class="px-4 py-3 text-left">Room Charges</th>
                <th class="px-4 py-3 text-left">Discount</th>
                <th class="px-4 py-3 text-left">Service Charge</th>
                <th class="px-4 py-3 text-left">Other Charge</th>
                <th class="px-4 py-3 text-left">GST</th>
                <th class="px-4 py-3 text-left">Total</th>
                <th class="px-4 py-3 text-left">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @foreach($bookings as $booking)
                <tr>
                    <td class="px-4 py-3">{{ $loop->iteration }}</td>
                    <td class="px-4 py-3">{{ $booking->booking_number }}</td>
                    <td class="px-4 py-3">
                        {{ $booking->customer_name }}<br>
                        <span class="text-xs text-gray-500">{{ $booking->customer_mobile }}</span>
                    </td>
                    <td class="px-4 py-3">
                        {{ $booking->company_name ?? 'N/A' }}<br>
                        <span class="text-xs text-gray-500">{{ $booking->gst_number ?? 'N/A' }}</span>
                    </td>
                    <td class="px-4 py-3">{{ $booking->check_in->format('d M Y') }}</td>
                    <td class="px-4 py-3">{{ $booking->number_of_nights }}</td>
                    <td class="px-4 py-3">₹{{ number_format($booking->room_charges, 2) }}</td>
                    <td class="px-4 py-3">₹{{ number_format($booking->discount_amount, 2) }}</td>
                    <td class="px-4 py-3">₹{{ number_format($booking->service_tax, 2) }}</td>
                    <td class="px-4 py-3">₹{{ number_format($booking->other_charges, 2) }}</td>
                    <td class="px-4 py-3">₹{{ number_format($booking->gst_amount, 2) }}</td>
                    <td class="px-4 py-3 font-semibold">₹{{ number_format($booking->total_amount, 2) }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs rounded
                            @if($booking->booking_status == 'confirmed') bg-blue-100 text-blue-800
                            @elseif($booking->booking_status == 'checked_out') bg-green-100 text-green-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($booking->booking_status) }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
</div>
@endsection
