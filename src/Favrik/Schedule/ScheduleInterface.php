<?php

namespace Favrik\Schedule;

interface ScheduleInterface
{

    /**
     * Get the next item to process.
     *
     * @return array
     */
    public function next();

    /**
     * Determine if there are items in the queue.
     *
     * @return boolean
     */
    public function valid();
}
