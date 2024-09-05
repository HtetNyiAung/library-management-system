<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Borrowing;
use App\Models\BookCopy;
use Illuminate\Http\Request;
use Validator;
use App\Http\Resources\BorrowingResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class BorrowingController extends BaseController
{

    public function __construct()
    {
        // Apply middleware to restrict access to certain methods
        $this->middleware('auth')->only('store', 'show', 'index'); // Users can access store, show, and index
        $this->middleware('auth:admin')->only(['update', 'destroy']); // Admins can access update and destroy
    }

    public function index(Request $request)
    {
        // Get the user_id from the request query parameters
        $userId = $request->query('user_id');

        // Build the query
        $query = Borrowing::with(['user', 'bookCopy'])->latest();

        // If user_id is provided, add a where condition to the query
        if ($userId) {
            $query->where('user_id', $userId);
        }

        // Execute the query and get the results
        $borrowings = $query->get();
        return $this->sendResponse(BorrowingResource::collection($borrowings), 'Borrowing retrieved successfully.');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'book_copy_id' => 'required|exists:book_copies,id',
            'borrowed_at' => 'required|date',
            'due_date' => 'required|date',
        ]);

        // Check if the book copy is available
        $bookCopy = BookCopy::findOrFail($validatedData['book_copy_id']);

        if ($bookCopy->status !== 'available') {
            return $this->sendError('This book copy is not available for borrowing.', 400);
        }

        // If the book copy is available, proceed with the borrowing
        $borrowing = Borrowing::create($validatedData);

        // Update the status of the specific book copy to 'borrowed'
        $bookCopy->update(['status' => 'borrowed']);

        return $this->sendResponse(new BorrowingResource($borrowing), 'Borrowing created successfully.');

    }

    public function show($id)
    {
        $borrowing = Borrowing::with(['user', 'bookCopy'])->findOrFail($id);

        if (is_null($borrowing)) {
            return $this->sendError('Borrowing not found.');
        }
       
        return $this->sendResponse(new BorrowingResource($borrowing), 'Borrowing retrieved successfully.');
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'user_id' => 'sometimes|required|exists:users,id',
            'book_copy_id' => 'sometimes|required|exists:book_copies,id',
            'borrowed_at' => 'sometimes|required|date',
            'due_date' => 'sometimes|required|date',
            'returned_at' => 'nullable|date',
        ]);

        // Find the borrowing record by ID
        $borrowing = Borrowing::findOrFail($id);

        if (is_null($borrowing)) {
            return $this->sendError('Borrowing not found.');
        }

        // Check if the book copy ID is being updated
        if (isset($validatedData['book_copy_id'])) {
            $bookCopy = BookCopy::findOrFail($validatedData['book_copy_id']);
        } else {
            $bookCopy = $borrowing->bookCopy; // Get the current book copy if not updated
        }

        // If the return date is provided, update the book copy status back to 'available'
        if (!empty($validatedData['returned_at'])) {
            $bookCopy->update(['status' => 'available']);
        }

        // Update the borrowing record with the validated data
        $borrowing->update($validatedData);

        return $this->sendResponse(new BorrowingResource($borrowing), 'Borrowing updated successfully.');
    }

    public function destroy($id)
    {
        $borrowing = Borrowing::findOrFail($id);

        if (is_null($borrowing)) {
            return $this->sendError('Borrowing not found.');
        }

        $borrowing->delete();
        return $this->sendResponse([], 'Borrowing deleted successfully.');
    }
}
