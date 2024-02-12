<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttendeeResource;
use App\Models\Event;
use Illuminate\Http\Request;

class AttendeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Event $event, Request $request)
    {
        $perPage = $request->query("per_page");

        $attendees = $event->attendees()->latest()->paginate($perPage ?? 10);
        // Return response json that include pagination meta
        return response()->json([
            "status" => "success",
            "message" => "Attendees of the event",
            "data" => AttendeeResource::collection($attendees),
            "meta" => [
                "total" => $attendees->total(),
                "per_page" => $attendees->perPage(),
                "current_page" => $attendees->currentPage(),
                "last_page" => $attendees->lastPage(),
                "from" => $attendees->firstItem(),
                "to" => $attendees->lastItem(),
                "prev_page_url" => $attendees->previousPageUrl(),
                "next_page_url" => $attendees->nextPageUrl(),
                "first_page_url" => $attendees->url(1),
                "last_page_url" => $attendees->url($attendees->lastPage()),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
