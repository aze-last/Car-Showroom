<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Unit;
use App\Models\UnitImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ShowroomSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all()->keyBy('name');
        
        if ($categories->isEmpty()) {
            return;
        }

        // 1. Recover legacy folders if they exist
        $legacyFolders = [14, 15, 16, 1, 2, 3];
        foreach ($legacyFolders as $id) {
            $path = "units/{$id}";
            if (Storage::disk('public')->exists($path)) {
                $unit = Unit::create([
                    'category_id' => $categories->get('Cars')?->id ?? $categories->first()->id,
                    'name' => "Legacy Unit {$id}",
                    'price_php' => rand(500000, 2000000),
                    'description' => 'Recovered legacy unit.',
                    'status' => Unit::STATUS_AVAILABLE,
                    'show_price' => true,
                ]);

                $files = Storage::disk('public')->files($path);
                foreach ($files as $index => $file) {
                    UnitImage::create([
                        'unit_id' => $unit->id,
                        'url' => $file,
                        'sort_order' => $index,
                    ]);
                }
            }
        }

        // 2. New Realistic Units
        $newUnits = [
            [
                'name' => 'Yamaha YZF-R1',
                'category' => 'Motorcycle',
                'price' => 1099000,
                'description' => 'The R1 features a next-generation R-series design, sophisticated electronic control, and a crossplane engine for ultimate performance.',
                'image_query' => 'yamaha,r1,motorcycle'
            ],
            [
                'name' => 'Honda Civic Type R',
                'category' => 'Cars',
                'price' => 3870000,
                'description' => 'The ultimate hot hatch. Engineered for speed and precision, the Civic Type R delivers an exhilarating driving experience on both road and track.',
                'image_query' => 'honda,civic,typer,car'
            ],
            [
                'name' => 'Toyota Fortuner GR Sport',
                'category' => 'Cars',
                'price' => 2550000,
                'description' => 'Dominate every road with the GR Sport edition. Rugged, powerful, and luxurious, it is the perfect companion for any adventure.',
                'image_query' => 'toyota,fortuner,suv'
            ],
            [
                'name' => 'Vespa Primavera 150',
                'category' => 'Motorcycle',
                'price' => 210000,
                'description' => 'Iconic Italian style meets modern technology. The Primavera is agile, stylish, and perfect for city cruising.',
                'image_query' => 'vespa,scooter'
            ],
            [
                'name' => 'Mazda MX-5 Miata',
                'category' => 'Sportscars',
                'price' => 2250000,
                'description' => 'The world\'s best-selling roadster. Lightweight, perfectly balanced, and pure driving joy.',
                'image_query' => 'mazda,mx5,miata,car'
            ],
        ];

        foreach ($newUnits as $data) {
            $unit = Unit::create([
                'category_id' => $categories->get($data['category'])?->id ?? $categories->first()->id,
                'name' => $data['name'],
                'price_php' => $data['price'],
                'description' => $data['description'],
                'status' => Unit::STATUS_AVAILABLE,
                'show_price' => true,
                'is_featured' => (rand(1, 10) > 6),
                'year' => rand(2022, 2025),
                'fuel_type' => 'Gasoline',
                'transmission' => 'Automatic',
            ]);

            $this->downloadPlaceholderImages($unit, $data['image_query']);
        }
    }

    private function downloadPlaceholderImages(Unit $unit, string $query)
    {
        $folder = "units/{$unit->id}";
        Storage::disk('public')->makeDirectory($folder);

        for ($i = 0; $i < 3; $i++) {
            $filename = (string) Str::uuid() . '.jpg';
            $path = "{$folder}/{$filename}";
            $url = "https://source.unsplash.com/featured/800x600/?{$query}&sig=" . rand(1, 1000);
            
            try {
                $contents = @file_get_contents($url);
                if ($contents) {
                    Storage::disk('public')->put($path, $contents);
                    UnitImage::create([
                        'unit_id' => $unit->id,
                        'url' => $path,
                        'sort_order' => $i,
                    ]);
                }
            } catch (\Exception $e) {}
        }
    }
}
