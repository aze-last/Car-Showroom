<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class DefaultCategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Motorcycle', 'Cars', 'Vans', 'Sportscars'] as $name) {
            Category::query()->updateOrCreate(
                ['name' => $name],
                ['name' => $name],
            );
        }
    }
}
