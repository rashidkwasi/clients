<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AllocationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing allocations
        DB::table('allocations')->truncate();
        
        // Read JSON file
        $json = File::get(database_path('seeders/AllocationsTableSeeder.json'));
        $data = json_decode($json, true);
        
        // Insert data
        foreach ($data as $item) {
            DB::table('allocations')->insert($item);
        }
    }
}