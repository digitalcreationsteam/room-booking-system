<!-- File: resources/views/rooms/create.blade.php -->
@extends('layouts.app')

@section('title', 'Create Room')
@section('header', 'Create New Room')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('rooms.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Room Number *</label>
                    <input type="text" name="room_number" value="{{ old('room_number') }}"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:border-blue-500" required>
                    @error('room_number')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Room Type *</label>
                    <select name="room_type_id" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-blue-500" required>
                        <option value="">Select Room Type</option>
                        @foreach($roomTypes as $type)
                            <option value="{{ $type->id }}" {{ old('room_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('room_type_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Floor Number *</label>
                    <input type="number" name="floor_number" value="{{ old('floor_number', 0) }}"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:border-blue-500" required>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Base Price (₹) *</label>
                    <input type="number" name="base_price" value="{{ old('base_price') }}" step="0.01"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:border-blue-500" required>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">GST % *</label>
                    <input type="number" name="gst_percentage" value="{{ old('gst_percentage', 12.00) }}" step="0.01"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:border-blue-500" required>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Service Tax %</label>
                    <input type="number" name="service_tax_percentage" value="{{ old('service_tax_percentage', 0) }}" step="0.01"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Other Charges (₹)</label>
                    <input type="number" name="other_charges" value="{{ old('other_charges', 0) }}" step="0.01"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:border-blue-500">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Amenities</label>
                <textarea name="amenities" rows="2" placeholder="AC, WiFi, TV, Mini Bar, etc."
                    class="w-full px-3 py-2 border rounded focus:outline-none focus:border-blue-500">{{ old('amenities') }}</textarea>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Status *</label>
                <select name="status" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-blue-500" required>
                    <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="booked" {{ old('status') == 'booked' ? 'selected' : '' }}>Booked</option>
                    <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                </select>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 px-6 py-2 rounded">
                    Create Room
                </button>
                <a href="{{ route('rooms.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
