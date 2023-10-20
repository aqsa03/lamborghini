<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupporterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('supporters')->insert([
            'email' => 'test@gmail.com',
            'trial_days' => 60
        ],[
            'email' => 'test2@gmail.com',
            'trial_days' => 60
        ]);
    }
}
