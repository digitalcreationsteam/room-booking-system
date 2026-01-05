@extends('layouts.app')

@section('title', 'Rooms')
@section('header', 'Rooms Management')

@section('content')

<div class="flex justify-between items-center mb-6">
    <h3 class="text-xl font-semibold text-gray-800">All Rooms</h3>

    <a href="{{ route('rooms.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">
        <i class="fas fa-plus mr-2"></i> Add New Room
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full border-collapse">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-xs uppercase text-left">Sr No</th>
                <th class="px-6 py-3 text-xs uppercase text-left">Room No</th>
                <th class="px-6 py-3 text-xs uppercase text-left">Type</th>
                <th class="px-6 py-3 text-xs uppercase text-left">Floor</th>
                <th class="px-6 py-3 text-xs uppercase text-left">Base Price</th>
                <th class="px-6 py-3 text-xs uppercase text-left">GST %</th>
                <th class="px-6 py-3 text-xs uppercase text-left">Status</th>
                <th class="px-6 py-3 text-xs uppercase text-center">Actions</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-200">
            @forelse($rooms as $room)
                <tr class="hover:bg-gray-50 transition">

                    <td class="px-6 py-4 font-semibold">{{ $loop->iteration }}</td>

                    <td class="px-6 py-4 font-semibold">{{ $room->room_number }}</td>

                    <td class="px-6 py-4">{{ $room->roomType->name }}</td>

                    <td class="px-6 py-4">{{ $room->floor_number }}</td>

                    <td class="px-6 py-4">
                        ₹{{ number_format($room->base_price, 2) }}
                    </td>

                    <td class="px-6 py-4">{{ $room->gst_percentage }}%</td>

                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs font-semibold rounded
                            @if($room->status === 'available') bg-green-100 text-green-800
                            @elseif($room->status === 'booked') bg-orange-100 text-orange-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst($room->status) }}
                        </span>
                    </td>

                    {{-- ACTION BUTTONS --}}
                    <td class="px-6 py-4">
                        <div class="flex justify-center gap-2">

                            {{-- View --}}
                            <a href="{{ route('rooms.show', $room) }}"
                               title="View Room"
                               class="w-9 h-9 flex items-center justify-center bg-blue-50 text-blue-600 rounded hover:bg-blue-100">
                                <i class="fas fa-eye"></i>
                            </a>

                            {{-- Edit --}}
                            <a href="{{ route('rooms.edit', $room) }}"
                               title="Edit Room"
                               class="w-9 h-9 flex items-center justify-center bg-yellow-50 text-yellow-600 rounded hover:bg-yellow-100">
                                <i class="fas fa-edit"></i>
                            </a>

                            {{-- Delete --}}
                            @if($room->status !== 'booked')
                                <form action="{{ route('rooms.destroy', $room) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        title="Delete Room"
                                        onclick="return confirm('Are you sure you want to delete this room?')"
                                        class="w-9 h-9 flex items-center justify-center bg-red-50 text-red-600 rounded hover:bg-red-100">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @else
                                <button
                                    title="Room is booked"
                                    class="w-9 h-9 flex items-center justify-center bg-gray-100 text-gray-400 rounded cursor-not-allowed">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endif

                        </div>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-6 text-center text-gray-500">
                        No rooms found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
     <div class="mt-4 px-6 mb-4">
    {{ $rooms->links() }}
</div>
</div>

@endsection
