<?php

namespace Tests\Unit;

use Tests\TestCase;
use Favrik\Elevator;
use Favrik\Schedule\SCANSchedule;

class SCANScheduleTest extends TestCase
{
    private $schedule;

    protected function setUp()
    {
        $this->idCounter = 1;

        $this->schedule = new SCANSchedule([
                $this->createRequest(1, 6, 1),
                $this->createRequest(2, 5, 7),
                $this->createRequest(3, 3, 0),
                $this->createRequest(4, 0, 7),
            ],
            (object) Elevator::getDefaultState()
        );
    }

    protected function createRequest($id, $from, $to)
    {
        return SCANSchedule::createRequest($id, $from, $to);
    }

    public function testHasRequest()
    {
        $this->assertTrue($this->schedule->valid());
    }

    public function testSort()
    {
        $this->assertEquals($this->createRequest(4, 0, 5), $this->schedule->next());
        $this->assertEquals($this->createRequest(2, 5, 7), $this->schedule->next());
        $this->assertEquals($this->createRequest(1, 6, 3), $this->schedule->next());
        $this->assertEquals($this->createRequest(3, 3, 1), $this->schedule->next());
        $this->assertEquals($this->createRequest(null, 1, 0), $this->schedule->next());
        $this->assertFalse($this->schedule->next());
    }

    public function testDifferentStartingFloor()
    {
        $state = new \stdClass;
        $state->current_floor = 6;
        $this->schedule->setState($state);

        $this->assertEquals($this->createRequest(1, 6, 3), $this->schedule->next());
        $this->assertEquals($this->createRequest(3, 3, 1), $this->schedule->next());
        $this->assertEquals($this->createRequest(null, 1, 0), $this->schedule->next());
        $this->assertEquals($this->createRequest(4, 0, 5), $this->schedule->next());
        $this->assertEquals($this->createRequest(2, 5, 7), $this->schedule->next());
        $this->assertFalse($this->schedule->next());
    }
}
