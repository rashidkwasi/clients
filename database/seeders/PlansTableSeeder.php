<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class PlansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing plans
        DB::table('plans')->truncate();
        
        // Read JSON file
        $json = File::get(database_path('seeders/PlansTableSeeder.json'));
        $data = json_decode($json, true);
        
        // Insert data
        foreach ($data as $item) {
            DB::table('plans')->insert($item);
        }
    }
}