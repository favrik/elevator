<?php

namespace Favrik\Schedule;

use Favrik\Schedule\ScheduleInterface;

class FirstComeFirstServedSchedule implements ScheduleInterface
{
    private $queue;

    public function __construct()
    {
        $this->queue = new \SplQueue();
        $this->queue->setIteratorMode(\SplQueue::IT_MODE_DELETE);
    }

    public function add($from, $to)
    {
        $this->queue->push([$from, $to]);
    }

    public function next()
    {
        if (!$this->queue->isEmpty()) {
            return $this->queue->shift();
        }

        return false;
    }
}
