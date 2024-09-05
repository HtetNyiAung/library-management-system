<?php

namespace App\Http\Controllers\API;
       
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\BookCopy;
use Validator;
use App\Http\Resources\BookCopyResource;
use Illuminate\Http\JsonResponse;


class BookCopyController extends BaseController
{
     /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bookCopy = BookCopy::with('book')->latest()->get();
        
        return $this->sendResponse(BookCopyResource::collection($bookCopy), 'Book Copies retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'copy_number' => 'required|integer',
            'status' => 'required|string|in:available,borrowed',
        ]);

        $bookCopy = BookCopy::create($request->all());
        return $this->sendResponse(new BookCopyResource($bookCopy), 'Book Copies created successfully.');
    }

     /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $bookCopy = BookCopy::with('book')->findOrFail($id);

        return $this->sendResponse(new BookCopyResource($bookCopy), 'bookCopy retrieved successfully.');
    }

     /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'book_id' => 'sometimes|required|exists:books,id',
            'copy_number' => 'sometimes|required|integer',
            'status' => 'sometimes|required|string|in:available,borrowed',
        ]);

        $bookCopy = BookCopy::findOrFail($id);
        $bookCopy->update($request->all());

        return $this->sendResponse(new BookCopyResource($bookCopy), 'bookCopy updated successfully.');
    }

    public function destroy($id)
    {
        $bookCopy = BookCopy::findOrFail($id);
        $bookCopy->delete();
        return $this->sendResponse([], 'bookCopy deleted successfully.');
    }
}
