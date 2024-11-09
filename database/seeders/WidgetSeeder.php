<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WidgetModel;

class WidgetSeeder extends Seeder
{
    public function run()
    {
        $colors = ['red', 'blue', 'green', 'white', 'black'];
        $sizes = ['small', 'medium', 'large'];

        for ($i = 1; $i <= 16; $i++) {
            WidgetModel::create([
                'name' => 'Widget ' . $i,
                'color' => $colors[array_rand($colors)],
                'size' => $sizes[array_rand($sizes)],
                'count' => rand(1, 100), // Random count between 1 and 100
                'active' => (bool)rand(0, 1), // Random active status (true or false)
            ]);
        }
    }
}
