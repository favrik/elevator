<?php

namespace App;

use Illuminate\Support\Facades\Log;
use Validator;
use App\Elevator;
use App\ElevatorRequest;
use App\Jobs\ProcessElevatorRequest;
use Favrik\Elevator as ElevatorEntity;
use Favrik\Schedule\FirstComeFirstServedSchedule as FIFOSchedule;

class ElevatorService
{
    const ELEVATOR_ID = 1;

    private $elevator;

    public function status()
    {
        return $this->getElevator();
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

    public function handleRequests()
    {
        $this->elevator = $this->getElevator();
        $elevatorLogic = new ElevatorEntity($this->elevator, $this);
        $scheduler = new FIFOSchedule($this->getPendingRequests());

        if (!$elevatorLogic->canReceiveRequest()) {
            return;
        }

        $this->updateState(['processing' => 1]);
        while ($request = $scheduler->next()) {
            // Request ids can be null, in case the scheduler algo created
            // intermediate requests (not in the db).
            if (isset($request->id)) {
                $this->updateState(['request_id' => $request->id]);
            }

            $elevatorLogic->request($request);

            if (isset($request->id)) {
                ElevatorRequest::destroy($request->id);
            }
        }

        $this->updateState(['request_id' => 0, 'processing' => 0]);
        $requests = $this->getPendingRequests();
        if (!empty($requests)) {
            dispatch(new ProcessElevatorRequest($this));
        }
    }

    public function updateState($state)
    {
        Log::info('Elevator state', $state);
        $this->elevator->fill($state)->save();
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
        Elevator::where('id', self::ELEVATOR_ID)->update(
            ElevatorEntity::getDefaultState()
        );
    }

    private function getElevator()
    {
        return Elevator::find(self::ELEVATOR_ID);
    }

    private function getPendingRequests()
    {
        return ElevatorRequest::orderBy('id', 'ASC')->get()->all();
    }

}
