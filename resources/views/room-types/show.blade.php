<!-- File: resources/views/room-types/show.blade.php -->
@extends('layouts.app')

@section('title', 'Room Type Details')
@section('header', 'Room Type Details - ' . $roomType->name)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Details -->
    <div class="lg:col-span-2">
        <!-- Room Type Information -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-lg font-semibold">Room Type Information</h3>
                @if($roomType->is_active)
                    <a href="{{ route('room-types.edit', $roomType) }}" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Room Type Name</p>
                    <p class="font-semibold">{{ $roomType->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Base Price</p>
                    <p class="font-semibold text-blue-600">₹{{ number_format($roomType->base_price, 2) }}/night</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Maximum Occupancy</p>
                    <p class="font-semibold">{{ $roomType->max_occupancy ?? 'N/A' }} Guests</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Status</p>
                    <span class="px-3 py-1 text-sm rounded {{ $roomType->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $roomType->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                @if($roomType->description)
                    <div class="col-span-2">
                        <p class="text-sm text-gray-600">Description</p>
                        <p class="font-semibold">{{ $roomType->description }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Amenities/Features -->
        @if($roomType->amenities)
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">Amenities & Features</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach(json_decode($roomType->amenities) as $amenity)
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-2"></i>
                            <span>{{ $amenity }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Rooms List -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Rooms in this Type</h3>
                <a href="{{ route('rooms.create', ['room_type_id' => $roomType->id]) }}"
                    class="text-blue-600 hover:text-blue-800">
                    <i class="fas fa-plus mr-1"></i> Add Room
                </a>
            </div>

            @if($roomType->rooms->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Room Number</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Floor</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Availability</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($roomType->rooms as $room)
                                <tr>
                                    <td class="px-4 py-3">
                                        <span class="font-semibold">{{ $room->room_number }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $room->floor ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 text-xs rounded
                                            @if($room->status == 'available') bg-green-100 text-green-800
                                            @elseif($room->status == 'occupied') bg-red-100 text-red-800
                                            @elseif($room->status == 'maintenance') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($room->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($room->is_active)
                                            <span class="text-green-600"><i class="fas fa-check-circle"></i> Active</span>
                                        @else
                                            <span class="text-red-600"><i class="fas fa-times-circle"></i> Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('rooms.show', $room) }}" class="text-blue-600 hover:text-blue-800 mr-3" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('rooms.edit', $room) }}" class="text-green-600 hover:text-green-800 mr-3" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No rooms found for this room type</p>
            @endif
        </div>
    </div>

    <!-- Sidebar -->
    <div>
        <!-- Quick Stats -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Quick Statistics</h3>

            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Rooms:</span>
                    <span class="font-bold text-2xl text-blue-600">{{ $roomType->rooms->count() }}</span>
                </div>

                <div class="flex justify-between items-center border-t pt-4">
                    <span class="text-gray-600">Available:</span>
                    <span class="font-semibold text-green-600">{{ $roomType->rooms->where('status', 'available')->count() }}</span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Occupied:</span>
                    <span class="font-semibold text-red-600">{{ $roomType->rooms->where('status', 'occupied')->count() }}</span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Maintenance:</span>
                    <span class="font-semibold text-yellow-600">{{ $roomType->rooms->where('status', 'maintenance')->count() }}</span>
                </div>

                <div class="flex justify-between items-center border-t pt-4">
                    <span class="text-gray-600">Active Rooms:</span>
                    <span class="font-semibold">{{ $roomType->rooms->where('is_active', true)->count() }}</span>
                </div>
            </div>
        </div>

        <!-- Pricing Info -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Pricing Information</h3>

            <div class="space-y-3">
                <div class="bg-blue-50 p-4 rounded">
                    <p class="text-sm text-gray-600 mb-1">Base Price</p>
                    <p class="text-2xl font-bold text-blue-600">₹{{ number_format($roomType->base_price, 2) }}</p>
                    <p class="text-xs text-gray-500">per night</p>
                </div>

                @if($roomType->weekend_price)
                    <div class="bg-purple-50 p-4 rounded">
                        <p class="text-sm text-gray-600 mb-1">Weekend Price</p>
                        <p class="text-xl font-bold text-purple-600">₹{{ number_format($roomType->weekend_price, 2) }}</p>
                        <p class="text-xs text-gray-500">per night</p>
                    </div>
                @endif

                @if($roomType->extra_bed_charge)
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Extra Bed Charge:</span>
                        <span class="font-semibold">₹{{ number_format($roomType->extra_bed_charge, 2) }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Actions</h3>

            <div class="space-y-2">
                <a href="{{ route('room-types.index') }}"
                    class="block w-full bg-gray-600 hover:bg-gray-700 text-white text-center px-4 py-2 rounded">
                    <i class="fas fa-arrow-left mr-2"></i> Back to List
                </a>

                @if($roomType->is_active)
                    <a href="{{ route('room-types.edit', $roomType) }}"
                        class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center px-4 py-2 rounded">
                        <i class="fas fa-edit mr-2"></i> Edit Room Type
                    </a>
                @endif

                <a href="{{ route('rooms.create', ['room_type_id' => $roomType->id]) }}"
                    class="block w-full bg-green-600 hover:bg-green-700 text-white text-center px-4 py-2 rounded">
                    <i class="fas fa-plus mr-2"></i> Add New Room
                </a>

                @if($roomType->rooms->count() == 0)
                    <form action="{{ route('room-types.destroy', $roomType) }}" method="POST"
                        onsubmit="return confirm('Are you sure you want to delete this room type?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
                            <i class="fas fa-trash mr-2"></i> Delete Room Type
                        </button>
                    </form>
                @else
                    <div class="bg-yellow-50 border border-yellow-200 rounded p-3">
                        <p class="text-xs text-yellow-800">
                            <i class="fas fa-info-circle mr-1"></i>
                            Cannot delete room type with existing rooms
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
