<?php

/*
First there is an elevator class.
It has a direction (up, down, stand, maintenance), a current floor and a list of floor requests sorted in the direction.
Each elevator has a set of signals: Alarm, Door open, Door close

The scheduling will be like:
if available pick a standing elevator for this floor.
else pick an elevator moving to this floor.
else pick a standing elevator on another floor.

Sample data:
- Elevator standing in first floor
- Request from 6th floor go down to ground(first floor).
- Request from 5th floor go up to 7th floor
- Request from 3rd floor go down to ground
- Request from ground go up to 7th floor.
- Floor 2 and 4 are in maintenance.

Please use Bootstrap to implement the U/I.
Extra Point: Making an API to send/receive requests to elevator and write log file.
 */

namespace Favrik;


class Elevator {

    const DIRECTIONS = ['up', 'down', 'stand', 'maintenance'];
    const SIGNALS = ['alarm', 'open', 'closed'];

    /**
     * Current direction of the elevator. Default is standing idle.
     *
     * @var string
     */
    private $direction;

    /**
     * Current signal state in the elevator. Default is closed.
     *
     * @var string
     */
    private $signal;

    /**
     * Where the elevator is located.  Defaults to first floor.
     *
     * @var int
     */
    private $currentFloor;

    private $testing;

    /**
     * How much time does the Elevator takes to cover the distance between
     * adjacent floors, in seconds.
     *
     * @var int
     */
    private $floorTravelTime = 2;

    private $standingOpenTime = 2;

    private $storage;
    private $service;

    public function __construct($storage, $service)
    {
        $this->storage = $storage;
        $this->service = $service;
        $this->setState();
    }

    private function setState()
    {
        $this->currentFloor = $this->storage->current_floor;
        $this->signal = $this->storage->signal;
        $this->direction = $this->storage->direction;
        $this->testing = $this->storage->testing;
    }

    public function getState()
    {
        return [
            'current_floor' => $this->currentFloor,
            'signal' => $this->signal,
            'direction' => $this->direction,
        ];
    }

    public static function getDefaultState()
    {
        return [
            'current_floor' => 1,
            'signal' => 'closed',
            'direction' => 'stand',
            'request_id' => 0,
            'testing' => 0,
            'processing' => 0,
        ];
    }

    private function setDirection($direction)
    {
        if (in_array($direction, self::DIRECTIONS)) {
            $this->direction = $direction;
        }
    }

    public function triggerSignal($signal)
    {
        if (in_array($signal, self::SIGNALS)) {
            $this->signal = $signal;
        }
    }

    public function hasSignal($signal)
    {
        return $this->signal === $signal;
    }

    public function getFloor()
    {
        return $this->currentFloor;
    }

    public function getDirection()
    {
        return $this->direction;
    }

    public function setInMaintenance()
    {
        $this->direction = 'maintenance';
    }

    public function canReceiveRequest()
    {
        if ($this->hasSignal('alarm')
            || in_array($this->direction, ['maintenance', 'up', 'down'])
            || $this->storage->processing
        ) {
            return false;
        }

        return true;
    }

    public function request($request)
    {
        if ($request->to === $this->currentFloor) {
            $this->onArrival();
            return true;
        }

        return $this->onStart($request);
    }

    private function onArrival()
    {
        $this->openDoors();
    }

    private function onStart($request)
    {
        $this->moveToStart($request);

        $distance = $request->from - $request->to;
        $this->openDoors();
        $this->setDirection($distance > 0 ? 'down' : 'up');
        $this->syncState();

        if ($this->currentFloor != $request->from) {
            throw new Exception('invalid floor onStart');
        }

        $this->travel($this->currentFloor, $request->to);
        $this->onArrival();

        return true;
    }

    private function moveToStart($request)
    {
        $distance = $this->currentFloor - $request->from;
        if ($distance === 0) { // Already at start position.
            return;
        }

        $this->setDirection($distance > 0 ? 'down' : 'up');
        $this->triggerSignal('closed');
        $this->syncState();

        $this->travel($this->currentFloor, $request->from);
    }

    private function travel($from, $to)
    {
        $floors = range($from, $to);
        array_shift($floors);
        foreach ($floors as $floor) {
            $this->delay($this->floorTravelTime);
            $this->currentFloor = $floor;
            $this->syncState();
        }
    }

    private function openDoors()
    {
        $this->triggerSignal('open');
        $this->setDirection('stand');
        $this->syncState();

        $this->delay($this->standingOpenTime);

        $this->triggerSignal('closed');
        $this->syncState();
    }

    private function delay($seconds)
    {
        if ($this->testing) {
            return;
        }

        sleep($seconds);
    }

    private function syncState()
    {
        $this->service->updateState($this->getState());
    }
}
