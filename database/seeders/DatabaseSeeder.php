<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Store owner (admin) account
        User::create([
            'name' => 'Store Admin',
            'email' => 'admin@dropship.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Sample customer account
        User::create([
            'name' => 'Jamie Customer',
            'email' => 'customer@dropship.test',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);

        $categories = collect(['Home & Kitchen', 'Electronics', 'Fitness', 'Pet Supplies'])
            ->map(fn ($name) => Category::create(['name' => $name, 'slug' => Str::slug($name)]));

        $suppliers = collect([
            ['name' => 'Global Goods Co', 'contact_email' => 'sales@globalgoods.example', 'country' => 'China'],
            ['name' => 'EuroSource Ltd', 'contact_email' => 'orders@eurosource.example', 'country' => 'Germany'],
        ])->map(fn ($s) => Supplier::create($s));

        $tags = collect(['bestseller', 'new', 'eco-friendly', 'clearance'])
            ->map(fn ($name) => Tag::create(['name' => $name]));

        $sampleProducts = [
            ['name' => 'Stainless Steel Water Bottle', 'price' => 24.99, 'stock' => 150],
            ['name' => 'Wireless Earbuds Pro', 'price' => 59.99, 'stock' => 80],
            ['name' => 'Adjustable Resistance Bands Set', 'price' => 19.99, 'stock' => 200],
            ['name' => 'Automatic Pet Feeder', 'price' => 45.00, 'stock' => 40],
            ['name' => 'LED Desk Lamp', 'price' => 32.50, 'stock' => 60],
            ['name' => 'Yoga Mat Non-Slip', 'price' => 27.00, 'stock' => 120],
        ];

        foreach ($sampleProducts as $p) {
            $product = Product::create([
                'category_id' => $categories->random()->id,
                'supplier_id' => $suppliers->random()->id,
                'name' => $p['name'],
                'slug' => Str::slug($p['name']).'-'.Str::random(6),
                'description' => 'High quality '.strtolower($p['name']).' sourced directly from our supplier network.',
                'price' => $p['price'],
                'stock_quantity' => $p['stock'],
            ]);
            $product->tags()->attach($tags->random(rand(1, 2))->pluck('id'));
        }
    }
}