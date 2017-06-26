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

    /**
     * How much time does the Elevator takes to cover the distance between
     * adjacent floors, in seconds.
     *
     * @var int
     */
    private $floorTravelTime;

    private $requestTime;

    public function __construct()
    {
        $this->setDefaultState();
    }

    private function setDefaultState()
    {
        $this->currentFloor = 1;
        $this->signal = 'closed';
        $this->direction = 'stand';
        $this->floorTravelTime = 1;
    }

    public function getDefaultState()
    {
        return [
            'current_floor' => $this->currentFloor,
            'signal' => $this->signal,
            'direction' => $this->direction,
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
        if ($this->hasSignal('alarm')) {
            return false;
        }

        if (in_array($this->direction, ['maintenance', 'up', 'down'])) {
            return false;
        }

        return true;
    }

    public function reset()
    {
        if ($this->hasSignal('alarm') || $this->direction === 'maintenance') {
            $this->setDefaultState();
            return true;
        }

        return false;
    }


    public function request($from, $to)
    {
        if ($this->canReceiveRequest()) {
            $this->requestTime = time();

            $distance = $from - $to;
            if ($distance === 0) {
                return true;
            }

            if ($distance > 0) {
                $this->setDirection('down');
            }

            if ($distance < 0) {
                $this->setDirection('up');
            }

            while (true) {
                yield $this->requestStatus($distance);
            }
        }

        return false;
    }

    private function requestStatus($distance)
    {
        $duration = $distance * $this->floorTravelTime;
        $elapsedTime = time() - $this->requestTime;

        if ($elapsedTime >= $duration) {
            
        }
    }
}
