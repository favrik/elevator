<?php

namespace Favrik\Schedule;

use Favrik\Schedule\ScheduleInterface;

class FirstComeFirstServedSchedule implements ScheduleInterface
{
    private $queue;

    public function __construct($requests)
    {
        $this->queue = $requests;
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
}
