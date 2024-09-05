<?php

namespace App\Http\Controllers\API;
       
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Validator;
use App\Http\Resources\BookResource;
use App\Services\BookService;
use Illuminate\Http\JsonResponse;

class BookController extends BaseController
{

    protected $bookService;

    public function __construct(BookService $bookService)
    {
        $this->middleware('admin')->only(['store', 'update', 'destroy']);
        $this->bookService = $bookService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $books = $this->bookService->getAllBooks();
        
        return $this->sendResponse(BookResource::collection($books), 'Books retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|max:13|unique:books',
            'published_at' => 'required|date',
            'category_id' => 'required|exists:categories,id',
        ]);

        $book = $this->bookService->createBook($validatedData);

        return $this->sendResponse(new BookResource($book), 'Book created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $book = $this->bookService->getBookById($id);
      
        if (is_null($book)) {
            return $this->sendError('Book not found.');
        }
       
        return $this->sendResponse(new BookResource($book), 'Book retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'author' => 'sometimes|required|string|max:255',
            'isbn' => 'sometimes|required|string|max:13|unique:books,isbn,' . $id,
            'published_at' => 'sometimes|required|date',
            'category_id' => 'sometimes|required|exists:categories,id',
        ]);

        $book = $this->bookService->updateBook($id, $validatedData);

        if (is_null($book)) {
            return $this->sendError('Book not found.');
        }

        return $this->sendResponse(new BookResource($book), 'Book updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $deleted = $this->bookService->deleteBook($id);
        
        if (!$deleted) {
            return $this->sendError('Book not found.');
        }
        
        return $this->sendResponse([], 'Book deleted successfully.');
    }
}
