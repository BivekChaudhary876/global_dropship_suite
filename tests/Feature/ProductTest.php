<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A logged-in user can create a product, and it actually lands in the
     * database attached to them (core requirement 3, exercised end-to-end).
     */
    public function test_authenticated_user_can_create_a_product(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Electronics']);
        $supplier = Supplier::create(['name' => 'Acme Supplier']);

        $response = $this->actingAs($user)->post('/products', [
            'name' => 'Wireless Earbuds',
            'description' => 'Noise cancelling earbuds',
            'price' => 49.99,
            'cost_price' => 20.00,
            'stock' => 100,
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
        ]);

        $response->assertRedirect('/products');
        $this->assertDatabaseHas('products', [
            'name' => 'Wireless Earbuds',
            'user_id' => $user->id,
        ]);
    }

    /**
     * Server-side validation rejects bad input (core requirement 4) -
     * a negative price and a non-existent category should both fail,
     * and no row should be written.
     */
    public function test_product_creation_rejects_invalid_data(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/products', [
            'name' => '', // required field left empty
            'price' => -10, // fails min:0
            'cost_price' => 5,
            'stock' => 3,
            'category_id' => 9999, // fails exists:categories,id
            'supplier_id' => 9999,
        ]);

        $response->assertSessionHasErrors(['name', 'price', 'category_id', 'supplier_id']);
        $this->assertDatabaseCount('products', 0);
    }

    /**
     * Ownership authorization (core requirement 5): a user who is not the
     * owner (and not an admin) cannot delete someone else's product, even
     * by hitting the route directly.
     */
    public function test_user_cannot_delete_another_users_product(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create(['role' => 'seller']);
        $category = Category::create(['name' => 'Fitness']);
        $supplier = Supplier::create(['name' => 'FitCo']);

        $product = Product::create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
            'name' => 'Yoga Mat',
            'price' => 25,
            'cost_price' => 10,
            'stock' => 50,
        ]);

        $response = $this->actingAs($intruder)->delete("/products/{$product->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }
}