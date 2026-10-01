<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_a_product_with_invalid_data_fails_validation(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/products', [
            'name' => '',
            'price' => -5,
            'stock_quantity' => -1,
            'category_id' => 9999,
        ]);

        $response->assertSessionHasErrors(['name', 'price', 'stock_quantity', 'category_id']);
        $this->assertDatabaseCount('products', 0);
    }

    public function test_admin_can_create_a_valid_product(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->post('/admin/products', [
            'name' => 'Test Gadget',
            'description' => 'A perfectly valid gadget.',
            'price' => 19.99,
            'stock_quantity' => 10,
            'category_id' => $category->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('products', ['name' => 'Test Gadget']);
    }
}