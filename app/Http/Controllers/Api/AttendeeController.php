<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttendeeResource;
use App\Http\Traits\CanLoadRelationships;
use App\Models\Attendee;
use App\Models\Event;
use Illuminate\Http\Request;

class AttendeeController extends Controller
{

    use CanLoadRelationships;

    private array $relations;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
        $this->authorizeResource(Attendee::class, 'attendee');
        $this->relations = ['user'];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Event $event, Request $request)
    {
        $perPage = $request->query("per_page");
        $event = $this->loadRelationships($event);
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
    public function store(Request $request, Event $event)
    {
        $attendee = $this->loadRelationships($event->attendees()->create([
            "user_id" =>  $request->user()->id,
        ]));

        return response()->json([
            "status" => "success",
            "message" => "Attendee created",
            "data" => new AttendeeResource($attendee),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event, Attendee $attendee)
    {
        return response()->json([
            "status" => "success",
            "message" => "Attendee of the event",
            "data" => new AttendeeResource($this->loadRelationships($attendee)),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Event $event, Attendee $attendee)
    {
        // if ($request->user()->cannot('delete-attendee', [$event, $attendee])) {
        //     return response()->json([
        //         'message' => 'You are not authorized to delete this attendee!',
        //     ], 403);
        // }

        $attendee->delete();
        return response(status: 204);
    }
}
