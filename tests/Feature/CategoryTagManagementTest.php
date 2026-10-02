<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTagManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_add_a_category(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/categories', [
            'name' => 'Outdoor Gear',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', ['name' => 'Outdoor Gear']);
    }

    public function test_admin_can_delete_an_unused_category(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->delete("/admin/categories/{$category->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_admin_cannot_delete_a_category_with_products(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($admin)->delete("/admin/categories/{$category->id}");

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_admin_can_rename_a_category(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create(['name' => 'Old Category']);

        $response = $this->actingAs($admin)->patch("/admin/categories/{$category->id}", [
            'name' => 'New Category',
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'New Category']);
    }

    public function test_renaming_a_category_to_an_existing_name_fails_validation(): void
    {
        $admin = User::factory()->admin()->create();
        Category::factory()->create(['name' => 'Taken Name']);
        $category = Category::factory()->create(['name' => 'Original Name']);

        $response = $this->actingAs($admin)->patch("/admin/categories/{$category->id}", [
            'name' => 'Taken Name',
        ]);

        $response->assertSessionHasErrors(['name']);
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Original Name']);
    }

    public function test_admin_can_add_a_tag(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/tags', [
            'name' => 'clearance-sale',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tags', ['name' => 'clearance-sale']);
    }

    public function test_admin_can_delete_a_tag(): void
    {
        $admin = User::factory()->admin()->create();
        $tag = Tag::factory()->create();

        $response = $this->actingAs($admin)->delete("/admin/tags/{$tag->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }

    public function test_admin_can_rename_a_tag(): void
    {
        $admin = User::factory()->admin()->create();
        $tag = Tag::factory()->create(['name' => 'old-name']);

        $response = $this->actingAs($admin)->patch("/admin/tags/{$tag->id}", [
            'name' => 'new-name',
        ]);

        $response->assertRedirect(route('admin.tags.index'));
        $this->assertDatabaseHas('tags', ['id' => $tag->id, 'name' => 'new-name']);
    }

    public function test_renaming_a_tag_to_an_existing_name_fails_validation(): void
    {
        $admin = User::factory()->admin()->create();
        Tag::factory()->create(['name' => 'taken-name']);
        $tag = Tag::factory()->create(['name' => 'original-name']);

        $response = $this->actingAs($admin)->patch("/admin/tags/{$tag->id}", [
            'name' => 'taken-name',
        ]);

        $response->assertSessionHasErrors(['name']);
        $this->assertDatabaseHas('tags', ['id' => $tag->id, 'name' => 'original-name']);
    }
}