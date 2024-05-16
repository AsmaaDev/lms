<?php

namespace App\Http\Controllers\Api;


use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\BookResource;
use Illuminate\Support\Facades\Validator;



class BookController 
{
    // public function index()
    // {
    //     $books =  Book::get();
    //     return ApiController::respondWithSuccess(BookResource::collection($books));
    // }

    // public function show($id)
    // {
    //     try {
    //         $book = Book::findOrFail($id);
    //         return ApiController::respondWithSuccess(new BookResource($book));
    //     } catch (ModelNotFoundException $e) {
    //         return ApiController::respondWithNotFound();
    //     }
    // }

    public function index()
    {
        try {
            $books = Cache::remember('books', 60, function () {
                return Book::get();
            });

            return ApiController::respondWithSuccess(BookResource::collection($books));
        } catch (\Exception $e) {
            return ApiController::respondWithError('Failed to retrieve books.', 500);
        }
    }

    public function show($id)
    {
        try {
            $key = 'book_' . $id;

            $book = Cache::remember($key, 60, function () use ($id) {
                return Book::findOrFail($id);
            });

            return ApiController::respondWithSuccess(new BookResource($book));
        } catch (ModelNotFoundException $e) {
            return ApiController::respondWithNotFound();
        } catch (\Exception $e) {
            return ApiController::respondWithError('Failed to retrieve book details.', 500);
        }
    }


    
    public function store(Request $request)
    {
        // Validation rules
        $rules = [
            'title' => 'required|string',
            'author' => 'required|string',
            'publication_year' => 'required|date_format:Y',
            'isbn' => 'required|string|unique:books,isbn', 
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ApiController::respondWithError($validator->errors(), 422);
        }

        try {
            // Create a new book instance
            $book = Book::create($request->all());
            
            // Return success response with the created book resource
            return ApiController::respondWithSuccess(new BookResource($book), 'Book created successfully', 201);
        } catch (\Exception $e) {
            // Return error response if an exception occurs
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, Book $book)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publication_year' => 'required|date_format:Y',
            'isbn' => 'required|string|max:20|unique:books,isbn,' . $book->id, 
        ];


        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ApiController::respondWithError($validator->errors(), 422);
        }

        $updated = $book->update($request->all());
        
        return $updated
            ? ApiController::respondWithSuccess(new BookResource($book), 'Book updated successfully', 200)
            : ApiController::respondWithServerError('Failed to update the book.');
    }

    public function destroy($id)
    {
        try {
            // Find the book by ID and delete it
            $book = Book::findOrFail($id);
            $book->delete();
            
            // Return a success response with status code 204 (No Content)
            return ApiController::respondWithSuccess('Book deleted successfuly', 204);
        } catch (\Exception $e) {
            
            // If the Book is not found, return a JSON response with a 404 status code
            if ($e instanceof ModelNotFoundException) {
                return ApiController::respondWithError('Book not found', 404);
            }

            // For any other exception, return a JSON response with a 500 status code
            return ApiController::respondWithError('Internal Server Error', 500);
        }
    }
}
