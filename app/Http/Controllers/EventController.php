<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::orderBy('id', 'desc')->get();

        return response()->json([
            'status' => true,
            'message' => 'get events data success',
            'data' => $events,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|max:20',
            'name' => 'required|max:255',
            'description' => 'required',
            'location' => 'required|max:45',
            // 'start_time' => 'required',
            // 'time_ends' => 'required',
        ]);

        $event = Event::create($request->all());

        $response['status'] = true;
        $response['message'] = 'Create event success.';
        $response['data'] = $event;

        return response()->json($response);
    }

    public function show($id)
    {
        $event = Event::with('category:id,name')->findOrFail($id);

        $response['status'] = true;
        $response['data'] = $event;

        return response()->json($response);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'max:20',
            'name' => 'max:255',
            'location' => 'max:45',
            'file_link' => 'max:255',
            'img_link' => 'max:255',
        ]);

        $event = Event::findOrFail($id);
        $event->update($request->all());

        $response['status'] = true;
        $response['message'] = 'Update event success.';
        $response['data'] = $event;

        return response()->json($response);
    }

    public function setStatus(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        $event['status'] = $request->status;
        $event->save();

        $response['status'] = true;
        $response['message'] = 'Set status success.';
        $response['data'] = $event;

        return response()->json($response);
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();

        $response['status'] = true;
        $response['message'] = 'Delete event success.';
        $response['data'] = $event;

        return response()->json($response);
    }
}
