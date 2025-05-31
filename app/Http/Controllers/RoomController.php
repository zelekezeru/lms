<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoomStoreRequest;
use App\Http\Requests\RoomUpdateRequest;
use App\Http\Resources\RoomResource;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Room::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%');
            });
        }

        $allowedSorts = ['name', 'capacity'];
        $sortColumn = $request->sortColumn;
        $sortDirection = $request->sortDirection;

        if (in_array($sortColumn, $allowedSorts) && in_array($sortDirection, ['asc', 'desc'])) {
            $query->orderBy($sortColumn, $sortDirection);
        }

        $rooms = RoomResource::collection($query->paginate(15));

        return inertia('Rooms/Index', [
            'rooms' => $rooms,
            'sortInfo' => [
                'currentSortColumn' => $sortColumn,
                'currentSortDirection' => $sortDirection,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Rooms/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoomStoreRequest $request)
    {
        $fields = $request->validated();

        Room::create($fields);

        return redirect()->route('rooms.index')->with('success', 'Room created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room)
    {
        return inertia('Rooms/Show', [
            'room' => new RoomResource($room),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Room $room)
    {
        return inertia('Rooms/Edit', [
            'room' => new RoomResource($room),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoomUpdateRequest $request, Room $room)
    {
        $room->update($request->validated());

        return redirect()->route('rooms.index')->with('success', 'Room updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room)
    {
        $room->delete();

        return redirect()->route('rooms.index')->with('success', 'Room deleted successfully.');
    }
}
