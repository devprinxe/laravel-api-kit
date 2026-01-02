<?php

namespace Database\Seeders;

use App\Models\VariationType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VariationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        VariationType::factory(10)->create();
    }
}
