<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\ElevatorRequest;
use App\Elevator;
use App\ElevatorService;
use App\Jobs\ProcessElevatorRequest;

class ElevatorAPITest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->json('DELETE', '/api/reset');
    }

    public function enableElevatorTestingMode()
    {
        Elevator::where('id', ElevatorService::ELEVATOR_ID)->update(['testing' => 1]);
    }

    public function testRequestJobIsPushed()
    {
        Queue::fake();

        $response = $this->json('post', '/api/request', ['from' => 1, 'to' => 3]);
        $response
            ->assertStatus(200)
            ->assertJson([
                'request_id' => true,
            ]);

        Queue::assertPushed(ProcessElevatorRequest::class);
    }

    public function testSingleRequest()
    {
        $this->enableElevatorTestingMode();

        $response = $this->json('post', '/api/request', ['from' => 1, 'to' => 6]);

        $response = $this->json('GET', '/api/status');
        $response
            ->assertStatus(200)
            ->assertJson([
                'signal' => 'closed',
                'direction' => 'stand',
                'current_floor' => 6,
                'request' => null,
            ]);
    }

    public function testDefaultStatus()
    {
        $response = $this->json('GET', '/api/status');
        $response
            ->assertStatus(200)
            ->assertJson([
                'signal' => 'closed',
                'direction' => 'stand',
                'current_floor' => 1,
                'request' => null,
            ]);
    }

    public function testReset()
    {
        Queue::fake();

        $response = $this->json('POST', '/api/request', ['from' => 1, 'to' => 3]);
        $response = $this->json('DELETE', '/api/reset');
        $response
            ->assertStatus(200)
            ->assertJson([
                'reset' => 1,
            ]);

        $this->assertEquals(0, ElevatorRequest::all()->count());
    }
}
