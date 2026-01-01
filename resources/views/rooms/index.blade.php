<!-- File: resources/views/rooms/index.blade.php -->
@extends('layouts.app')

@section('title', 'Rooms')
@section('header', 'Rooms Management')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h3 class="text-xl font-semibold">All Rooms</h3>
    <a href="{{ route('rooms.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
        <i class="fas fa-plus mr-2"></i> Add New Room
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Room Number</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Floor</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Base Price</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">GST %</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($rooms as $room)
                <tr>
                    <td class="px-6 py-4 font-semibold">{{ $room->room_number }}</td>
                    <td class="px-6 py-4">{{ $room->roomType->name }}</td>
                    <td class="px-6 py-4">{{ $room->floor_number }}</td>
                    <td class="px-6 py-4">₹{{ number_format($room->base_price, 2) }}</td>
                    <td class="px-6 py-4">{{ $room->gst_percentage }}%</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded
                            @if($room->status == 'available') bg-green-100 text-green-800
                            @elseif($room->status == 'booked') bg-orange-100 text-orange-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst($room->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('rooms.show', $room) }}" class="text-blue-600 hover:text-blue-800 mr-3">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('rooms.edit', $room) }}" class="text-blue-600 hover:text-blue-800 mr-3">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('rooms.destroy', $room) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No rooms found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
