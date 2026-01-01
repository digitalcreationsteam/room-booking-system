<!-- File: resources/views/room-types/index.blade.php -->
@extends('layouts.app')

@section('title', 'Room Types')
@section('header', 'Room Types Management')

@section('content')
{{-- <div class="flex justify-between items-center mb-6">
    <h3 class="text-xl font-semibold">All Room Types</h3>
    <a href="{{ route('room-types.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
        <i class="fas fa-plus mr-2"></i> Add New Room Type
    </a>
</div> --}}

<x-page-header title="All Room Types">
    <a href="{{ route('room-types.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
        <i class="fas fa-plus mr-2"></i>
        Add New Room Type
    </a>
</x-page-header>


<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Base Price</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Rooms</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($roomTypes as $type)
                <tr>
                    <td class="px-6 py-4">{{ $type->name }}</td>
                    <td class="px-6 py-4">₹{{ number_format($type->base_price, 2) }}</td>
                    <td class="px-6 py-4">{{ $type->rooms_count }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded {{ $type->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $type->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('room-types.edit', $type) }}" class="text-blue-600 hover:text-blue-800 mr-3">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('room-types.destroy', $type) }}" method="POST" class="inline">
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
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No room types found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
