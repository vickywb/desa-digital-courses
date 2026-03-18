<?php

namespace App\Http\Controllers\Api\V1\HeadVillage;

use App\Http\Controllers\Controller;
use App\Http\Requests\EventStoreRequest;
use App\Services\EventService;
use Illuminate\Http\Request;

class EventController extends Controller
{

    public function __construct(
        private EventService $eventService
    ){}

    public function index()
    {
        //
    }

    public function store(EventStoreRequest $request)
    {
        $data = $request->validated();
        $eventFile = $request->file('file');
        dd($data, $eventFile);

        try {
        $event = $this->eventService->createEvent($data, $eventFile);

        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function show(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
