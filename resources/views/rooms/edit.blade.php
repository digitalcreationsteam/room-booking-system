@extends('layouts.app')

@section('title', 'Edit Room')
@section('header', 'Edit Room')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-lg shadow p-6">

        <form action="{{ route('rooms.update', $room->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Room Number & Room Type --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Room Number</label>
                    <input type="text" name="room_number"
                        value="{{ old('room_number', $room->room_number) }}"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:border-blue-500">

                    @error('room_number')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Room Type</label>
                    <select name="room_type_id"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:border-blue-500">
                        <option value="">Select Room Type</option>
                        @foreach($roomTypes as $type)
                            <option value="{{ $type->id }}"
                                {{ old('room_type_id', $room->room_type_id) == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('room_type_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Floor & Base Price --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Floor Number</label>
                    <input type="number" name="floor_number"
                        value="{{ old('floor_number', $room->floor_number) }}"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:border-blue-500">

                    @error('floor_number')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Base Price (₹)</label>
                    <input type="number" name="base_price"
                        value="{{ old('base_price', $room->base_price) }}" step="0.01"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:border-blue-500">

                    @error('base_price')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Taxes --}}
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">GST %</label>
                    <input type="number" name="gst_percentage"
                        value="{{ old('gst_percentage', $room->gst_percentage) }}" step="0.01"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:border-blue-500">

                    @error('gst_percentage')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Service Tax %</label>
                    <input type="number" name="service_tax_percentage"
                        value="{{ old('service_tax_percentage', $room->service_tax_percentage) }}" step="0.01"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:border-blue-500">

                    @error('service_tax_percentage')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Other Charges (₹)</label>
                    <input type="number" name="other_charges"
                        value="{{ old('other_charges', $room->other_charges) }}" step="0.01"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:border-blue-500">

                    @error('other_charges')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Amenities --}}
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Amenities</label>
                <textarea name="amenities" rows="2"
                    class="w-full px-3 py-2 border rounded focus:outline-none focus:border-blue-500"
                    placeholder="AC, WiFi, TV, Mini Bar, etc.">{{ old('amenities', $room->amenities) }}</textarea>

                @error('amenities')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status --}}
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                <select name="status"
                    class="w-full px-3 py-2 border rounded focus:outline-none focus:border-blue-500">
                    <option value="">Select Status</option>
                    <option value="available"
                        {{ old('status', $room->status) == 'available' ? 'selected' : '' }}>
                        Available
                    </option>
                    <option value="booked"
                        {{ old('status', $room->status) == 'booked' ? 'selected' : '' }}>
                        Booked
                    </option>
                    <option value="maintenance"
                        {{ old('status', $room->status) == 'maintenance' ? 'selected' : '' }}>
                        Maintenance
                    </option>
                </select>

                @error('status')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Buttons --}}
            <div class="flex gap-4">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">
                    Update Room
                </button>

                <a href="{{ route('rooms.index') }}"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded">
                    Cancel
                </a>
            </div>

        </form>
    </div>
</div>
@endsection
