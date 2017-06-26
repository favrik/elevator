<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\ElevatorRequest;

class ElevatorAPITest extends TestCase
{
    use DatabaseTransactions;

    public function testRequest()
    {
        $response = $this->json('POST', '/api/request', ['from' => 1, 'to' => 3]);
        $response
            ->assertStatus(200)
            ->assertJson([
                'request_id' => true,
            ]);
    }


    public function testStatus()
    {
        $response = $this->json('GET', '/api/status');
        $response
            ->assertStatus(200)
            ->assertJson([
                'signal' => 'closed',
                'direction' => 'stand',
                'current_floor' => 1,
                'request_id' => 0
            ]);
    }

    public function testReset()
    {
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
