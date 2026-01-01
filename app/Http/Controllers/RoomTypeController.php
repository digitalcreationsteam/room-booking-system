<?php
namespace App\Http\Controllers;

use App\Models\RoomType;
use Illuminate\Http\Request;

class RoomTypeController extends Controller
{
    public function index()
    {
        $roomTypes = RoomType::withCount('rooms')->latest()->get();
        return view('room-types.index', compact('roomTypes'));
    }

    public function create()
    {
        return view('room-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        RoomType::create($validated);

        return redirect()->route('room-types.index')
            ->with('success', 'Room Type created successfully!');
    }

    public function show(RoomType $roomType)
    {
        $roomType->load('rooms');
        return view('room-types.show', compact('roomType'));
    }

    public function edit(RoomType $roomType)
    {
        return view('room-types.edit', compact('roomType'));
    }

    public function update(Request $request, RoomType $roomType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        $roomType->update($validated);

        return redirect()->route('room-types.index')
            ->with('success', 'Room Type updated successfully!');
    }

    public function destroy(RoomType $roomType)
    {
        if ($roomType->rooms()->count() > 0) {
            return redirect()->route('room-types.index')
                ->with('error', 'Cannot delete room type with existing rooms!');
        }

        $roomType->delete();

        return redirect()->route('room-types.index')
            ->with('success', 'Room Type deleted successfully!');
    }
}
