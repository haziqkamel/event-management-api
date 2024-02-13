<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Http\Traits\CanLoadRelationships;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{

    use CanLoadRelationships;

    private array $relations;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
        $this->relations = ['user', 'attendees', 'attendees.user'];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $this->loadRelationships(Event::query());
        $perPage = $request->query("per_page");

        $events = $query->latest()->paginate($perPage ?? 10);

        return response()->json([
            'message' => 'Successfully retrieved events!',
            'data' => EventResource::collection($events),
                'meta' => [
                    'total' => $events->total(),
                    'per_page' => $events->perPage(),
                    'current_page' => $events->currentPage(),
                    'last_page' => $events->lastPage(),
                    'from' => $events->firstItem(),
                    'to' => $events->lastItem(),
                    "prev_page_url" => $events->previousPageUrl(),
                    "next_page_url" => $events->nextPageUrl(),
                    "first_page_url" => $events->url(1),
                    "last_page_url" => $events->url($events->lastPage()),
                ],
        ]);
    }

    protected function shouldIncludeRelation(string $relation): bool
    {
        $include = request()->query('include');

        if (!$include) {
            return false;
        }

        $relations = array_map('trim', explode(',', $include));
        return in_array($relation, $relations);
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
                'user_id' => $request->user()->id,
            ]);

            return response()->json([
                'message' => 'Event created!',
                'data' => new EventResource($this->loadRelationships($event)),
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
            'data' => new EventResource($this->loadRelationships($event)),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {  
        try {
            $event->update($request->validate([
                'name'=> 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'start_time' => 'sometimes|date',
                'end_time' => 'sometimes|date|after:start_time',
            ]));

            return response()->json([
                'message' => 'Event updated!',
                'data' => new EventResource($this->loadRelationships($event)),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred!',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();
        return response(status: 204);
    }
}
