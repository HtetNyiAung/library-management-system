<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Category;

class CategorySeederTest extends TestCase
{
    /** @test */
    public function specific_categories_are_seeded_correctly()
    {
        // Run the seeder
        $this->seed(\Database\Seeders\CategorySeeder::class);

        // List of expected category names
        $expectedCategories = [
            'Fiction', 'Non-Fiction', 'Science', 'Technology', 
            'History', 'Biography', 'Adventure', 'Fantasy', 
            'Mystery', 'Romance'
        ];

        // Assert that each expected category exists in the database
        foreach ($expectedCategories as $categoryName) {
            $this->assertDatabaseHas('categories', ['name' => $categoryName]);
        }
    }
}
