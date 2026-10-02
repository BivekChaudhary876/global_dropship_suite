<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->timestamps();
        });

        // Backfill: every product that already had a single image_path
        // gets that same photo as the first row in its new gallery, so
        // existing products show up with one image instead of none.
        DB::table('products')
            ->whereNotNull('image_path')
            ->select('id', 'image_path')
            ->get()
            ->each(function ($product) {
                DB::table('product_images')->insert([
                    'product_id' => $product->id,
                    'path' => $product->image_path,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};