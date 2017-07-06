@extends('layouts.app')

@section('title', 'Elevator Control Panel')

@section('heading', 'Elevator Visualizer with Control Panel')

@section('content')
<div class="row">
    <div class="elevator">
    @for ($i = 7; $i >= 1; $i--)
        <div id="floor-{{ $i }}" class="elevator__floor">{{ ordinal($i) }} floor</div>
    @endfor
    </div>
    <div class="control">
        <h2>Current Status</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Current Floor</th>
                    <th>Direction</th>
                    <th>Signal</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td id="floor"></td>
                    <td id="direction"></td>
                    <td id="signal"></td>
                </tr>
            </tbody>
        </table>

        <h2>Send Requests</h2>
        <form class="form-inline">
            <div class="form-group">
                <label for="exampleInputName2">From</label>
                <select id="from" class="form-control">
                    @for ($i = 1; $i <= 7; $i++)
                        <option value="{{$i}}">{{ ordinal($i) }} floor</option>
                    @endfor
                </select>
            </div>
            <div class="form-group">
                <label for="exampleInputEmail2">To</label>
                <select id="to" class="form-control">
                    @for ($i = 1; $i <= 7; $i++)
                        <option value="{{$i}}">{{ ordinal($i) }} floor</option>
                    @endfor
                </select>
            </div>
            <div class="checkbox">
                    <input id="bulk" type="checkbox" name="bulk" value="1" /> 
                <label for="bulk">Bulk Request?
                </label>
            </div>
            <button style="display: none" id="add-request" type="submit" class="btn btn-default">Add Request</button>
            <button id="request" type="submit" class="btn btn-primary">Send Request</button>
        </form>
        <h3>Queue</h3>
        <ul class="queue" id="queue"></ul>


        <h2>Options</h2>
        <p>
            <button id="reset" type="submit" class="btn btn-danger">Reset Status</button>
            <button id="trigger" type="submit" class="btn btn-warning">Trigger Alarm</button>
        </p>

    </div>
</div>

<script>
var E_STATE = {!! $state !!};
</script>
@endsection

