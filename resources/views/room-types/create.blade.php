@extends('layouts.app')

@section('title', 'Create Room Type')
@section('header', 'Create New Room Type')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('room-types.store') }}" method="POST">
            @csrf

            {{-- Room Type Name --}}
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Room Type Name *</label>
                <input type="text" name="name" value="{{ old('name') }}"
                    class="w-full px-3 py-2 border rounded focus:outline-none focus:border-blue-500">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                <textarea name="description" rows="3"
                    class="w-full px-3 py-2 border rounded focus:outline-none focus:border-blue-500">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Base Price --}}
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Base Price (₹) *</label>
                <input type="number" name="base_price" value="{{ old('base_price') }}" step="0.01"
                    class="w-full px-3 py-2 border rounded focus:outline-none focus:border-blue-500">
                @error('base_price')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status --}}
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1"
                        {{ old('is_active', true) ? 'checked' : '' }}
                        class="mr-2">
                    <span class="text-gray-700">Active</span>
                </label>
                @error('is_active')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Buttons --}}
            <div class="flex gap-4">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">
                    Create Room Type
                </button>

                <a href="{{ route('room-types.index') }}"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded">
                    Cancel
                </a>
            </div>

        </form>
    </div>
</div>
@endsection
