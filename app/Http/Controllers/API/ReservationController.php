<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Reservation;
use Validator;
use App\Http\Resources\ReservationResource;
use Illuminate\Http\JsonResponse;

class ReservationController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get the user_id from the request query parameters
        $userId = $request->query('user_id');

        // Build the query
        $query = Reservation::with(['user', 'book'])->latest();

        // If user_id is provided, add a where condition to the query
        if ($userId) {
            $query->where('user_id', $userId);
        }

        // Execute the query and get the results
        $reservations = $query->get();
        return $this->sendResponse(ReservationResource::collection($reservations), 'Reservations retrieved successfully.');
    }

     /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'book_id' => 'required|exists:books,id',
            'reserved_at' => 'required|date'
        ]);

        $reservation = Reservation::create([
            'user_id' => $validatedData['user_id'],
            'book_id' => $validatedData['book_id'],
            'reserved_at' => $validatedData['reserved_at'],
            'status' => 'active'  // Automatically set to 'active'
        ]);

        return $this->sendResponse(new ReservationResource($reservation), 'Reservation created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $reservation = Reservation::with(['user', 'book'])->findOrFail($id);

        if (is_null($reservation)) {
            return $this->sendError('Reservation not found.');
        }

        return $this->sendResponse(new ReservationResource($reservation), 'Reservation retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
         // Validate the status input
        $validatedData = $request->validate([
            'status' => 'required|string|in:active,canceled',
        ]);

        // Find the reservation by ID or fail
        $reservation = Reservation::findOrFail($id);

        // Update the reservation status
        $reservation->update(['status' => $validatedData['status']]);

        return $this->sendResponse(new ReservationResource($reservation), 'Reservation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);

        if (is_null($reservation)) {
            return $this->sendError('Reservation not found.');
        }

        $reservation->delete();
        
        return $this->sendResponse([], 'Reservation deleted successfully.');
    }
}
