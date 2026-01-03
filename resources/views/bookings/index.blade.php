@extends('layouts.app')

@section('title', 'Bookings')
@section('header', 'Bookings Management')

@section('content')

<x-page-header title="All Bookings">
    <a href="{{ route('bookings.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
        <i class="fas fa-plus mr-2"></i>
        New Booking
    </a>
</x-page-header>

{{-- Filters --}}
<div class="bg-white rounded-lg shadow p-4 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
            <label class="text-sm font-medium">From Date</label>
            <input type="date" name="from_date" value="{{ request('from_date') }}"
                   class="w-full px-3 py-2 border rounded">
        </div>

        <div>
            <label class="text-sm font-medium">To Date</label>
            <input type="date" name="to_date" value="{{ request('to_date') }}"
                   class="w-full px-3 py-2 border rounded">
        </div>

        <div>
            <label class="text-sm font-medium">Booking Status</label>
            <select name="status" class="w-full px-3 py-2 border rounded">
                <option value="">All</option>
                @foreach(['confirmed','checked_in','checked_out','cancelled'] as $status)
                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_',' ',$status)) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-sm font-medium">Payment Status</label>
            <select name="payment_status" class="w-full px-3 py-2 border rounded">
                <option value="">All</option>
                @foreach(['pending','partial','paid'] as $pay)
                    <option value="{{ $pay }}" {{ request('payment_status') == $pay ? 'selected' : '' }}>
                        {{ ucfirst($pay) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex items-end">
            <button class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                <i class="fas fa-filter mr-2"></i> Filter
            </button>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-xs uppercase">Booking #</th>
                <th class="px-6 py-3 text-xs uppercase">Reg. No</th>
                <th class="px-6 py-3 text-xs uppercase">Customer</th>
                <th class="px-6 py-3 text-xs uppercase">Dates</th>
                <th class="px-6 py-3 text-xs uppercase">Rooms</th>
                <th class="px-6 py-3 text-xs uppercase">Amount</th>
                <th class="px-6 py-3 text-xs uppercase">Payment</th>
                <th class="px-6 py-3 text-xs uppercase">Status</th>
                <th class="px-6 py-3 text-xs uppercase text-center">Actions</th>
            </tr>
        </thead>

        <tbody class="divide-y">
        @forelse($bookings as $booking)
            <tr>
                <td class="px-6 py-4 font-semibold">{{ $booking->booking_number }}</td>
                <td class="px-6 py-4">
                    {{ $booking->registration_no ?? '-' }}
                </td>
                <td class="px-6 py-4">
                    <div>{{ $booking->customer_name }}</div>
                    <div class="text-xs text-gray-500">{{ $booking->customer_mobile }}</div>
                </td>

                <td class="px-6 py-4 text-sm">
                    <div>In: {{ $booking->check_in->format('d M Y') }}</div>
                    <div>Out: {{ $booking->check_out->format('d M Y') }}</div>
                </td>

                <td class="px-6 py-4">
                    {{ $booking->bookingRooms->count() }} room(s)
                </td>

                <td class="px-6 py-4">
                    ₹{{ number_format($booking->total_amount,2) }}
                </td>

                {{-- Payment --}}
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded
                        {{ $booking->payment_status == 'paid' ? 'bg-green-100 text-green-800' :
                           ($booking->payment_status == 'partial' ? 'bg-yellow-100 text-yellow-800' :
                           'bg-red-100 text-red-800') }}">
                        {{ ucfirst($booking->payment_status) }}
                    </span>
                </td>

                {{-- Booking Status --}}
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded
                        {{ $booking->booking_status == 'confirmed' ? 'bg-blue-100 text-blue-800' :
                           ($booking->booking_status == 'checked_in' ? 'bg-green-100 text-green-800' :
                           ($booking->booking_status == 'checked_out' ? 'bg-gray-200 text-gray-800' :
                           'bg-red-100 text-red-800')) }}">
                        {{ ucfirst(str_replace('_',' ',$booking->booking_status)) }}
                    </span>
                </td>

                {{-- ACTIONS --}}
                <td class="px-6 py-4">
                    <div class="flex justify-center gap-2">

                        {{-- View --}}
                        <a href="{{ route('bookings.show',$booking) }}"
                           class="w-9 h-9 flex items-center justify-center bg-blue-50 text-blue-600 rounded hover:bg-blue-100">
                            <i class="fas fa-eye"></i>
                        </a>

                        {{-- Edit --}}
                        @if($booking->booking_status !== 'cancelled')
                        <a href="{{ route('bookings.edit',$booking) }}"
                           class="w-9 h-9 flex items-center justify-center bg-yellow-50 text-yellow-600 rounded hover:bg-yellow-100">
                            <i class="fas fa-edit"></i>
                        </a>
                        @endif

                        {{-- Check-in --}}
                        @if($booking->booking_status === 'confirmed')
                        <form method="POST" action="{{ route('bookings.check-in',$booking) }}">
                            @csrf
                            <button class="w-9 h-9 bg-green-50 text-green-600 rounded hover:bg-green-100">
                                <i class="fas fa-sign-in-alt"></i>
                            </button>
                        </form>
                        @endif

                        {{-- Check-out --}}
                        @if($booking->booking_status === 'checked_in')
                        <form method="POST" action="{{ route('bookings.check-out',$booking) }}">
                            @csrf
                            <button class="w-9 h-9 bg-purple-50 text-purple-600 rounded hover:bg-purple-100">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </form>
                        @endif

                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                    No bookings found
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
    <div class="mt-4 px-6 mb-4">
        {{ $bookings->links() }}
    </div>
</div>
@endsection
