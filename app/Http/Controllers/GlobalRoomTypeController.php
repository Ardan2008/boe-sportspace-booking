<?php

namespace App\Http\Controllers;

use App\Models\GlobalRoomType;
use Illuminate\Http\Request;

class GlobalRoomTypeController extends Controller
{
    /** GET /admin/room-types — return all types as JSON */
    public function index()
    {
        return response()->json(GlobalRoomType::orderBy('name')->get(['id', 'name']));
    }

    /** POST /admin/room-types — create a new type, return it */
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100|unique:global_room_types,name']);

        $type = GlobalRoomType::create(['name' => trim($request->name)]);

        return response()->json(['id' => $type->id, 'name' => $type->name], 201);
    }

    /** PUT /admin/room-types/{id} — rename an existing type */
    public function update(Request $request, $id)
    {
        $type = GlobalRoomType::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100|unique:global_room_types,name,' . $id,
        ]);

        $type->update(['name' => trim($request->name)]);

        return response()->json(['id' => $type->id, 'name' => $type->name]);
    }

    /** DELETE /admin/room-types/{id} — remove a type */
    public function destroy($id)
    {
        GlobalRoomType::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }
}
