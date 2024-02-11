<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'message' => 'This is the index of events',
            'data' => Event::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        try {
            $event = Event::create([
                ...$request->validate([
                    'name' => 'required|string|max:255',
                    'description' => 'nullable|string',
                    'start_time' => 'required|date',
                    'end_time' => 'required|date|after:start_time',
                ]),
                'user_id' => 1, // TODO: change to the correct user
            ]);

            return response()->json([
                'message' => 'Event created!',
                'data' => $event
            ], 201);    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred!',
                'error' => $e->getMessage(),
            ], 422);
        }      
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        return response()->json([
            'message' => 'An event was found!',
            'data' => $event
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
