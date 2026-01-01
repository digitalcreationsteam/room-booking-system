@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')

<div class="grid grid-cols-1 md:grid-cols-4 gap-6">

    <div class="bg-white p-6 rounded shadow">
        <h3 class="text-sm text-gray-500">Total Bookings</h3>
        <p class="text-2xl font-bold">{{ \App\Models\Booking::count() }}</p>
    </div>

    <div class="bg-white p-6 rounded shadow">
        <h3 class="text-sm text-gray-500">Active Rooms</h3>
        <p class="text-2xl font-bold">{{ \App\Models\Room::where('status','available')->count() }}</p>
    </div>

    <div class="bg-white p-6 rounded shadow">
        <h3 class="text-sm text-gray-500">Checked In</h3>
        <p class="text-2xl font-bold">
            {{ \App\Models\Booking::where('booking_status','checked_in')->count() }}
        </p>
    </div>

    <div class="bg-white p-6 rounded shadow">
        <h3 class="text-sm text-gray-500">Revenue</h3>
        <p class="text-2xl font-bold text-green-600">
            ₹ {{ number_format(\App\Models\Booking::sum('total_amount'), 2) }}
        </p>
    </div>
</div>

@endsection
