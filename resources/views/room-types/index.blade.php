@extends('layouts.app')

@section('title', 'Room Types')
@section('header', 'Room Types Management')

@section('content')

<x-page-header title="All Room Types">
    <a href="{{ route('room-types.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">
        <i class="fas fa-plus mr-2"></i>
        Add New Room Type
    </a>
</x-page-header>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full border-collapse">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-xs uppercase text-left">Name</th>
                <th class="px-6 py-3 text-xs uppercase text-left">Base Price</th>
                <th class="px-6 py-3 text-xs uppercase text-left">Total Rooms</th>
                <th class="px-6 py-3 text-xs uppercase text-left">Status</th>
                <th class="px-6 py-3 text-xs uppercase text-center">Actions</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-200">
            @forelse($roomTypes as $type)
                <tr class="hover:bg-gray-50 transition">

                    <td class="px-6 py-4 font-medium">
                        {{ $type->name }}
                    </td>

                    <td class="px-6 py-4">
                        ₹{{ number_format($type->base_price, 2) }}
                    </td>

                    <td class="px-6 py-4">
                        {{ $type->rooms_count }}
                    </td>

                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs font-semibold rounded
                            {{ $type->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $type->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>

                    {{-- ACTION BUTTONS --}}
                    <td class="px-6 py-4">
                        <div class="flex justify-center gap-2">

                            {{-- Edit --}}
                            <a href="{{ route('room-types.edit', $type) }}"
                               title="Edit Room Type"
                               class="w-9 h-9 flex items-center justify-center bg-yellow-50 text-yellow-600 rounded hover:bg-yellow-100">
                                <i class="fas fa-edit"></i>
                            </a>

                            {{-- Delete --}}
                            @if($type->rooms_count == 0)
                                <form action="{{ route('room-types.destroy', $type) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        title="Delete Room Type"
                                        onclick="return confirm('Are you sure you want to delete this room type?')"
                                        class="w-9 h-9 flex items-center justify-center bg-red-50 text-red-600 rounded hover:bg-red-100">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @else
                                <button
                                    title="Cannot delete (rooms exist)"
                                    class="w-9 h-9 flex items-center justify-center bg-gray-100 text-gray-400 rounded cursor-not-allowed">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endif

                        </div>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-6 text-center text-gray-500">
                        No room types found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-4 px-6 mb-4">
    {{ $roomTypes->links() }}
</div>
</div>

@endsection
