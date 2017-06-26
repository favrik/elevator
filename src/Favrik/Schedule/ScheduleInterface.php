<?php

namespace Favrik\Schedule;

interface ScheduleInterface
{
    /**
     * Adds a new item to the schedule.
     *
     * @param int $from
     * @param int $to
     */
    public function add($from, $to);

    /**
     * Get the next item to process.
     *
     * @return array
     */
    public function next();
}
