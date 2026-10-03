<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'location' => 'Yangon',
        ]);

        foreach (Product::sampleProducts() as $product) {
            Product::query()->firstOrCreate(
                ['slug' => $product['slug']],
                [
                    'seller_id' => $user->id,
                    'name' => $product['name'],
                    'slug' => $product['slug'],
                    'category' => $product['category'],
                    'price' => $product['price'],
                    'location' => $product['location'],
                    'description' => $product['description'],
                    'image' => $product['image'],
                    'status' => $product['status'],
                ]
            );
        }
    }
}
