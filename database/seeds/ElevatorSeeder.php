<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ElevatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('elevator')->insert([
            'id' => 1,
            'current_floor' => 1,
            'direction' => 'stand',
            'signal' => 'closed',
            'request_id' => 0,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }
}
