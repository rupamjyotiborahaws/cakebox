<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('types')->insert([
            ['cake_type' => 'Vanilla'],
            ['cake_type' => 'Chocolate'],
            ['cake_type' => 'Strawberry'],
        ]);

        DB::table('flavors')->insert([
            ['flavor_name' => 'Straberry'],
            ['flavor_name' => 'Vannila'],
            ['flavor_name' => 'Rasmalai'],
        ]);

        DB::table('weight')->insert([
            ['cake_weight' => '250GM'],
            ['cake_weight' => '500GM'],
            ['cake_weight' => '1KG'],
            ['cake_weight' => '1.5KG'],
            ['cake_weight' => '2KG'],
        ]);

        DB::table('session_management')->insert([
            ['session_time' => 600]
        ]);
        
        DB::table('order_status')->insert([
            ['order_status' => 'Pending'],
            ['order_status' => 'Processing'],
            ['order_status' => 'Delivered'],
            ['order_status' => 'Cancelled']
        ]);
    }
}
