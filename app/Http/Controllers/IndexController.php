<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Events\ElevatorStateUpdated;

class IndexController extends Controller
{
    public function index()
    {
        return view('index');
    }

    /**
     * Manually publish on redis following the format by the Laravel Broadcaster.
     */
    public function send()
    {
        Redis::publish('elevator', json_encode(['event' => 'App\Events\ElevatorStateUpdated', 'data' => ['state' => 'hello']]));
    }
}
