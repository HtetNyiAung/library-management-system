<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\BookController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\BookCopyController;
use App\Http\Controllers\API\BorrowingController;
use App\Http\Controllers\API\ReservationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [RegisterController::class, 'login']);
     
Route::get('books', [BookController::class, 'index']);  
Route::get('books/{id}', [BookController::class, 'show']);

// (Authenticated users)
Route::middleware('auth:api')->group( function () {
   // Route for creating a new borrowing (store action) – available to users
    Route::post('borrowings', [BorrowingController::class, 'store'])->name('borrowings.store');

    // Route for viewing a single borrowing record (show action) – available to everyone or users
    Route::get('borrowings/{borrowing}', [BorrowingController::class, 'show'])->name('borrowings.show');

    // Route for listing all borrowings (index action) – available to admins or specific roles
    Route::get('borrowings', [BorrowingController::class, 'index'])->name('borrowings.index');

    // Route for listing all reservations (index action) – accessible to admins or specific roles
    Route::get('reservations', [ReservationController::class, 'index'])->name('reservations.index');

    // Route for creating a new reservation (store action) – accessible to users
    Route::post('reservations', [ReservationController::class, 'store'])->name('reservations.store');

    // Route for viewing a single reservation (show action) – accessible to users or admins
    Route::get('reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
});


// Admin routes (Authenticated admin users)
Route::middleware(['auth:api', 'admin'])->group(function () {

    Route::post('books', [BookController::class, 'store']);  // Add books (admin-only)
    Route::put('books/{id}', [BookController::class, 'update']); // Edit books (admin-only)
    Route::delete('books/{id}', [BookController::class, 'destroy']);  // Delete books (admin-only)

    // Route for updating a borrowing record (update action)
    Route::put('borrowings/{borrowing}', [BorrowingController::class, 'update'])->name('borrowings.update');
    // Route for deleting a borrowing record (destroy action) – restricted to admins
    Route::delete('borrowings/{borrowing}', [BorrowingController::class, 'destroy'])->name('borrowings.destroy');
     
    // Route for updating a reservation (update action) 
    Route::put('reservations/{reservation}', [ReservationController::class, 'update'])->name('reservations.update');

    // Route for deleting a reservation (destroy action) 
    Route::delete('reservations/{reservation}', [ReservationController::class, 'destroy'])->name('reservations.destroy');

    Route::apiResource('reservations', ReservationController::class);
    Route::apiResource('categories', CategoryController::class);  // Manage categories (admin-only)
    Route::apiResource('book-copies', BookCopyController::class); // Manage book copies (admin-only)
});





