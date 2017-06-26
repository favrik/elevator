<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ElevatorService;

class ElevatorController extends Controller
{
    protected $elevatorService;

    public function __construct(ElevatorService $elevatorService)
    {
        $this->elevatorService = $elevatorService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function status()
    {
        return response($this->elevatorService->status());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function request(Request $request)
    {
        if ($elevatorRequest = $this->elevatorService->sendRequest($request)) {
            return response()->json([
                'request_id' => $elevatorRequest->id,
            ]);
        }

        return response('Invalid request', 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function reset()
    {
        $this->elevatorService->reset();
        return response()->json(['reset' => 1]);
    }
}
