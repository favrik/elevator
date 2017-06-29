<?php

namespace Favrik\Schedule;

use Favrik\Schedule\ScheduleInterface;

class SCANSchedule implements ScheduleInterface
{
    public $queue;

    private $requests;
    private $state;

    public function __construct($requests, $initialState)
    {
        $this->requests = $requests;
        $this->state = $initialState;
        $this->sort();
    }

    public function setState($state)
    {
        $this->state = $state;
        $this->sort();
    }

    public function next()
    {
        if ($this->valid()) {
            return array_shift($this->queue);
        }

        return false;
    }

    public function valid()
    {
        return !empty($this->queue);
    }

    private function sort()
    {
        $this->setQueueDirection($this->buildQueue('up'), $this->buildQueue('down'));
    }

    private function setQueueDirection($up, $down)
    {
        $request = $this->closestRequest();
        $direction = $this->goingUp($request) ? 'up' : 'down';

        $args = [$up, $down];
        $this->queue = call_user_func_array(
            'array_merge',
            $direction == 'up' ? $args : array_reverse($args)
        );
    }

    private function closestRequest()
    {
        $distances = [];
        $requests = $this->sortRequests($this->requests, 'down');
        foreach ($requests as $request) {
            $distances[abs($this->state->current_floor - $request->from)] = $request;
        }

        return $distances[min(array_keys($distances))];
    }

    private function buildQueue($direction)
    {
        $requests = $this->getRequests($direction);
        $floors = $this->getFloors($requests);
        if ($direction == 'up') {
            sort($floors);
        } else {
            rsort($floors);
        }

        $count = count($floors) - 1;
        $sortedRequests = [];
        for ($i = 0; $i <= $count; $i++) {
            if ($i == $count) {
                break;
            }

            $sortedRequests[] = self::createRequest(
                isset($requests[$i]) ? $requests[$i]->id : null,
                $floors[$i],
                $floors[$i + 1]
            );
        }

        return $sortedRequests;
    }

    private function getFloors($requests) {
        $list = array_reduce($requests, function ($carry, $item) {
            $carry[] = $item->from;
            $carry[] = $item->to;
            return $carry;
        }, []);

        return array_unique($list);
    }

    public static function createRequest($id, $from, $to)
    {
        return (object) ['id' => $id, 'from' => $from, 'to' => $to];
    }

    private function getRequests($direction)
    {
        $requests = array_reduce($this->requests, function ($initial, $request) use ($direction) {
            if ($direction == 'up') {
                if ($this->goingUp($request)) {
                    $initial[] = $request;
                }
            } else {
                if ($this->goingDown($request)) {
                    $initial[] = $request;
                }
            }

            return $initial;
        }, []);

        return $this->sortRequests($requests, $direction);
    }

    private function sortRequests($requests, $direction)
    {
        usort($requests, function ($a, $b) use ($direction) {
            if ($a->from == $b->from) {
                return 0;
            }

            if ($direction == 'up') {
                return ($a->from > $b->from) ? 1 : -1;
            }

            return ($a->from < $b->from) ? 1 : -1;
        });

        return $requests;
    }

    private function goingUp($request)
    {
        return $this->getDirection($request) < 0;
    }

    private function goingDown($request)
    {
        return $this->getDirection($request) > 0;
    }

    private function getDirection($request)
    {
        return $request->from - $request->to;
    }
}
