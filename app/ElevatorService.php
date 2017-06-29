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
        $validator = $this->getValidator($request->all());
        if ($validator->fails()) {
            return false;
        }

        if ($this->isDuplicateRequest($request)) {
            return false;
        }

        $elevatorRequest = $this->createRequest($request);

        dispatch(new ProcessElevatorRequest($this));

        return $elevatorRequest;
    }

    public function sendBulkRequest($data)
    {
        if (!isset($data['requests']) || !is_array($data['requests'])) {
            return false;
        }

        $insert = [];
        foreach ($data['requests'] as [$from, $to]) {
            $request = ['from' => $from, 'to' => $to];
            $validator = $this->getValidator($request);
            if ($validator->fails()) {
                return false;
            }

            if ($this->isDuplicateRequest($request)) {
                return false;
            }

            $insert[] = array_merge(
                $request,
                ['created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]
            );
        }

        return ElevatorRequest::insert($insert);
    }

    }

    private function getValidator($data)
    {
        return Validator::make($data, [
            'from' => 'bail|integer|required',
            'to' => 'required|integer|different:from',
        ]);
    }

    private function isDuplicateRequest($request)
    {
        $count = ElevatorRequest::where('from', $request->input('from'))
            ->where('to', $request->input('to'))
            ->count();

        return $count > 0;
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
