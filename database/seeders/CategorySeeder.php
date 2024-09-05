<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Fiction'],
            ['name' => 'Non-Fiction'],
            ['name' => 'Science'],
            ['name' => 'Technology'],
            ['name' => 'History'],
            ['name' => 'Biography'],
            ['name' => 'Adventure'],
            ['name' => 'Fantasy'],
            ['name' => 'Mystery'],
            ['name' => 'Romance'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
