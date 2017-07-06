<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Events\ElevatorStateUpdated;
use App\ElevatorService;

class IndexController extends Controller
{
    public function index()
    {
        $service = new ElevatorService;
        return view('index', ['state' => $service->status()->toJson()]);
    }

    /**
     * Manually publish on redis following the format by the Laravel Broadcaster.
     */
    public function send()
    {
        Redis::publish('elevator', json_encode(['event' => 'App\Events\ElevatorStateUpdated', 'data' => ['state' => 'hello']]));
    }
}
