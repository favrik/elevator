<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ElevatorRequest;

class Elevator extends Model
{
    protected $table = 'elevator';

    protected $appends = ['request'];

    protected $fillable = ['current_floor', 'request_id', 'direction', 'signal', 'processing'];

    public function getRequestAttribute()
    {
        if ($request = ElevatorRequest::find($this->attributes['request_id'])) {
            return [$request->from, $request->to];
        }
    }
}
