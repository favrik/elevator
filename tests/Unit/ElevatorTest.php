<?php

namespace Tests\Unit;

use Tests\TestCase;
use Favrik\Elevator;
use App\Elevator as Storage;
use App\ElevatorService;

class ElevatorTest extends TestCase
{
    private $elevator;

    protected function setUp()
    {
        $this->elevator = new Elevator(
            (object) Elevator::getDefaultState(),
            new ElevatorService
        );
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

    public function testCannotReceiveRequestOnProcessing()
    {
        $storage = (object) Elevator::getDefaultState();
        $elevator = new Elevator($storage, new ElevatorService);

        $storage->processing = 1;

        $this->assertFalse($elevator->canReceiveRequest());
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

    public function testRequest()
    {
    }
}
