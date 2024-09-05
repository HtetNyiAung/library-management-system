<?php

namespace Tests\Unit;

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Category;
use App\Models\Book;

class BookControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Start a new database transaction
        DB::beginTransaction();

        // Create necessary data
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->user = User::factory()->create(['role' => 'user']);
        $this->category = Category::factory()->create();
    }

    protected function tearDown(): void
    {
        // Rollback the transaction
        DB::rollBack();

        parent::tearDown();
    }

    public function testAdminCanCreateBook()
    {
        $response = $this->actingAs($this->admin, 'api')
                        ->postJson('/api/books', [
                            'title' => 'New Book Title',
                            'author' => 'Author Name',
                            'isbn' => '1234567890123',
                            'published_at' => '2024-01-01',
                            'category_id' => $this->category->id,
                        ]);

        $response->assertStatus(200) // Ensure this is checking for 201 status code
                ->assertJson([
                    'message' => 'Book created successfully.'
                ]);
    }

    public function testUserCannotCreateBook()
    {
        // Simulate user authentication
        $this->actingAs($this->user, 'api');

        $response = $this->postJson('/api/books', [
            'title' => 'Test Book',
            'author' => 'Test Author',
            'isbn' => '1234567890123',
            'published_at' => '2024-01-01',
            'category_id' => $this->category->id,
        ]);

        $response->assertStatus(403)
                 ->assertJson(['error' => 'Unauthorized']);
    }

    public function testAdminCanUpdateBook()
    {
        // Create a book to update
        $book = Book::factory()->create([
            'category_id' => $this->category->id,
        ]);

        // Simulate admin authentication
        $this->actingAs($this->admin, 'api');

        $response = $this->putJson("/api/books/{$book->id}", [
            'title' => 'Updated Book Title',
            'author' => 'Updated Author',
            'isbn' => '1234567890123',
            'published_at' => '2024-02-01',
            'category_id' => $this->category->id,
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Book updated successfully.']);
    }

    public function testAdminCanDeleteBook()
    {
        // Create a book to delete
        $book = Book::factory()->create([
            'category_id' => $this->category->id,
        ]);

        // Simulate admin authentication
        $this->actingAs($this->admin, 'api');

        $response = $this->deleteJson("/api/books/{$book->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Book deleted successfully.']);
    }
}
