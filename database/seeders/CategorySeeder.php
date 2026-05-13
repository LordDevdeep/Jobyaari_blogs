<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Admit Card',  'color' => '#3b82f6'],
            ['name' => 'Latest Jobs', 'color' => '#10b981'],
            ['name' => 'Results',     'color' => '#f59e0b'],
            ['name' => 'Answer Key',  'color' => '#8b5cf6'],
            ['name' => 'Syllabus',    'color' => '#ef4444'],
        ];

        foreach ($categories as $row) {
            Category::updateOrCreate(
                ['name' => $row['name']],
                [
                    'slug' => Str::slug($row['name']),
                    'color' => $row['color'],
                ]
            );
        }
    }
}
