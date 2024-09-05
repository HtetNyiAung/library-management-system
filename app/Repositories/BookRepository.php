<?php

namespace App\Repositories;

use App\Models\Book;

class BookRepository implements BookRepositoryInterface
{
    public function getAll()
    {
        return Book::with('category')->latest()->get();
    }

    public function findById($id)
    {
        return Book::find($id);
    }

    public function create(array $data)
    {
        return Book::create($data);
    }

    public function update($id, array $data)
    {
        $book = $this->findById($id);

        if ($book) {
            $book->update($data);
            return $book;
        }

        return null;
    }

    public function delete($id)
    {
        $book = $this->findById($id);

        if ($book) {
            $book->delete();
            return true;
        }

        return false;
    }
}