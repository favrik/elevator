<?php

namespace App;

use Validator;
use App\Elevator;
use App\ElevatorRequest;
use Favrik\Elevator as ElevatorEntity;

class ElevatorService
{
    const ELEVATOR_ID = 1;

    public function status()
    {
        $elevator = Elevator::find(self::ELEVATOR_ID);
        return $elevator->toJson();
    }

    public function reset()
    {
        ElevatorRequest::truncate();
        $this->resetElevator();
    }

    public function sendRequest($request)
    {
        $validator = $this->getValidator($request);
        if ($validator->fails()) {
            return false;
        }

        return $this->createRequest($request);
    }

    private function getValidator($request)
    {
        return Validator::make($request->all(), [
            'from' => 'bail|integer|required',
            'to' => 'required|integer',
        ]);
    }

    private function createRequest($request)
    {
        $elevatorRequest = new ElevatorRequest;
        $elevatorRequest->from = $request->input('from');
        $elevatorRequest->to = $request->input('to');
        $elevatorRequest->save();

        return $elevatorRequest;
    }

    private function resetElevator()
    {
        $entity = new ElevatorEntity;
        $defaultState = $entity->getDefaultState();

        $elevator = Elevator::find(self::ELEVATOR_ID);
        $elevator->signal = $defaultState['signal'];
        $elevator->direction = $defaultState['direction'];
        $elevator->current_floor = $defaultState['current_floor'];

        $elevator->save();
    }
}
