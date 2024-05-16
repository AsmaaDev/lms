<?php

namespace App\Http\Controllers\Api;

use App\Models\Book;
use App\Models\Patron;
use App\Models\BorrowingRecord;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\BorrowingRecordResource;
use Illuminate\Support\Facades\Validator;

class BorrowingRecordController 
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $borrowing_records =  BorrowingRecord::get();
        return ApiController::respondWithSuccess(BorrowingRecordResource::collection($borrowing_records));
  
    }

    /**
     * Store a newly created resource in storage.
     */
   
    public function store(Request $request, $bookId, $patronId)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'borrowed_at' =>  'required|date|date_format:Y-m-d|after:yesterday',
        ], [
            'borrowed_at.date_format' => 'The borrowing date must be in the format "y-m-d".',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return ApiController::respondWithError($validator->errors(), 422);
        }

        try {
            // Find the book by its ID
            $book = Book::findOrFail($bookId);

            // Find the patron by their ID
            $patron = Patron::findOrFail($patronId);

            // Create a new borrowing record instance
            $borrowingRecord = new BorrowingRecord([
                'book_id' => $bookId,
                'patron_id' => $patronId,
                'borrowed_at' => $request->input('borrowed_at'),
            ]);

            // Save the borrowing record
            $borrowingRecord->save();

            // Return success response with the created Borrowing Record resource
            return ApiController::respondWithSuccess(
                true,
                'Borrowing Record created successfully',
                201
            );
        } catch (ModelNotFoundException $e) {
            // Return error response if book or patron not found
            return ApiController::respondWithError('Book or Patron not found', 404);
        } catch (\Exception $e) {
            // Return error response if an exception occurs
            return ApiController::respondWithError($e->getMessage(), 500);
        }
    }

  

    /**
     * Update the specified resource in storage.
     */
   

    public function returnBook(Request $request, $bookId, $patronId)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'returned_at' => 'required|date|date_format:Y-m-d|after:yesterday',
        ], [
            'returned_at.date_format' => 'The borrowing date must be in the format "y-m-d".',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return ApiController::respondWithError($validator->errors(), 422);
        }

        try {
            // Find the borrowing record associated with the book and patron
            $borrowingRecord = BorrowingRecord::where('book_id', $bookId)
                ->where('patron_id', $patronId)
                ->whereNull('returned_at')
                ->firstOrFail();

            // Update the borrowing record with the return date
            $borrowingRecord->returned_at = $request->input('returned_at');
            $borrowingRecord->save();

            // Return success response with the updated Borrowing Record resource
            return ApiController::respondWithSuccess(
                true,
                'Book returned successfully',
                200
            );
        } catch (ModelNotFoundException $e) {
            return ApiController::respondWithError('Borrowing record not found', 404);
        } catch (\Exception $e) {
            return ApiController::respondWithError($e->getMessage(), 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // Find the borrowing record by ID and delete it
            $borrowing_record = BorrowingRecord::findOrFail($id);
            $borrowing_record->delete();
            
            // Return a success response with status code 204 (No Content)
            return ApiController::respondWithSuccess('borrowing Record deleted successfuly', 204);
        } catch (\Exception $e) {
            
            // If the borrowing Record is not found, return a JSON response with a 404 status code
            if ($e instanceof ModelNotFoundException) {
                return ApiController::respondWithError('borrowing Record not found', 404);
            }

            // For any other exception, return a JSON response with a 500 status code
            return ApiController::respondWithError('Internal Server Error', 500);
        }
    }
}
