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
        return $this->successResponse($this->elevatorService->status());
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
            return $this->successResponse(['request_id' => $elevatorRequest->id]);
        }

        return $this->errorResponse();
    }

    public function bulkRequest(Request $request)
    {
        if ($requestIds = $this->elevatorService->sendBulkRequest($request->json()->all())) {
            return $this->successResponse(['request_ids' => $requestIds]);
        }

        return $this->errorResponse();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function reset()
    {
        $this->elevatorService->reset();
        return $this->successResponse(['reset' => 1]);
    }

    /**
     * @param Array $data
     */
    protected function successResponse($data)
    {
        return response()->json($data);
    }

    protected function errorResponse()
    {
        return response('Invalid request', 500);
    }
}
