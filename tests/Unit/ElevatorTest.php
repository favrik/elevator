<?php

namespace Tests\Unit;

use Tests\TestCase;
use Favrik\Elevator;

class ElevatorTest extends TestCase
{
    private $elevator;

    protected function setUp()
    {
        $this->elevator = new Elevator();
    }

    public function testDefaultSignal()
    {
        $this->assertTrue($this->elevator->hasSignal('closed'));
    }

    public function testSignalTrigger()
    {
        $this->elevator->triggerSignal('alarm');
        $this->assertTrue($this->elevator->hasSignal('alarm'));
    }

    public function testCannotSetInvalidSignal()
    {
        $this->elevator->triggerSignal('dummy');
        $this->assertFalse($this->elevator->hasSignal('alarm'));
    }

    public function testDefaultFloor()
    {
        $this->assertEquals(1, $this->elevator->getFloor());
    }

    public function testDefaultDirection()
    {
        $this->assertEquals('stand', $this->elevator->getDirection());
    }

    public function testCanReceiveRequest()
    {
        $this->assertTrue($this->elevator->canReceiveRequest());
    }

    public function testCannotReceiveRequestOnAlarm()
    {
        $this->elevator->triggerSignal('alarm');
        $this->assertFalse($this->elevator->canReceiveRequest());
    }

    public function testCannotReceiveRequestOnMaintenance()
    {
        $this->elevator->setInMaintenance();
        $this->assertFalse($this->elevator->canReceiveRequest());
    }

    public function testReset()
    {
        $this->elevator->setInMaintenance();
        $this->elevator->triggerSignal('alarm');
        $resetReturn = $this->elevator->reset();
        $this->assertTrue($this->elevator->canReceiveRequest());
        $this->assertTrue($resetReturn);
    }

    public function testInvalidReset()
    {
        $this->assertFalse($this->elevator->reset());
    }

    public function testRequest()
    {
        $this->elevator->request(1, 3);
    }
}
