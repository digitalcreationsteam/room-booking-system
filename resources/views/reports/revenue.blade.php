<!-- File: resources/views/reports/revenue.blade.php -->
@extends('layouts.app')

@section('title', 'Revenue Report')
@section('header', 'Revenue Report')

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

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-green-50 rounded-lg p-6 border border-green-200">
        <p class="text-sm text-gray-600">Total Revenue</p>
        <p class="text-3xl font-bold text-green-600">₹{{ number_format($summary['total_revenue'], 2) }}</p>
    </div>
    <div class="bg-blue-50 rounded-lg p-6 border border-blue-200">
        <p class="text-sm text-gray-600">Advance Collected</p>
        <p class="text-3xl font-bold text-blue-600">₹{{ number_format($summary['total_advance'], 2) }}</p>
    </div>
    <div class="bg-red-50 rounded-lg p-6 border border-red-200">
        <p class="text-sm text-gray-600">Pending Amount</p>
        <p class="text-3xl font-bold text-red-600">₹{{ number_format($summary['total_pending'], 2) }}</p>
    </div>
    <div class="bg-purple-50 rounded-lg p-6 border border-purple-200">
        <p class="text-sm text-gray-600">Avg Booking Value</p>
        <p class="text-3xl font-bold text-purple-600">₹{{ number_format($summary['avg_booking_value'], 2) }}</p>
    </div>
</div>

<!-- Daily Revenue -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b">
        <h3 class="text-lg font-semibold">Daily Revenue Breakdown</h3>
    </div>
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left">Sr No</th>
                <th class="px-6 py-3 text-left">Date</th>
                <th class="px-6 py-3 text-left">Bookings</th>
                <th class="px-6 py-3 text-left">Revenue</th>
                <th class="px-6 py-3 text-left">Advance</th>
                <th class="px-6 py-3 text-left">Pending</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @foreach($dailyRevenue as $day)
                <tr>
                    <td class="px-6 py-4 font-semibold"> {{ $loop->iteration }}</td>
                    <td class="px-6 py-4 font-semibold">{{ $day['date'] }}</td>
                    <td class="px-6 py-4">{{ $day['bookings'] }}</td>
                    <td class="px-6 py-4 text-green-600 font-semibold">₹{{ number_format($day['revenue'], 2) }}</td>
                    <td class="px-6 py-4">₹{{ number_format($day['advance'], 2) }}</td>
                    <td class="px-6 py-4 text-red-600">₹{{ number_format($day['pending'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
