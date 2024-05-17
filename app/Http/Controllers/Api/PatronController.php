<?php

namespace App\Http\Controllers\Api;

use App\Models\Patron;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\PatronResource;
use Illuminate\Support\Facades\Validator;

class PatronController 
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $patrons =  Patron::get();
        return ApiController::respondWithSuccess(PatronResource::collection($patrons));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function show($id)
    {
        try {
            $patron = Patron::findOrFail($id);
            return ApiController::respondWithSuccess(new PatronResource($patron));
        } catch (ModelNotFoundException $e) {
            return ApiController::respondWithNotFound();
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string',
            'phone' => 'required|string|regex:/^[0-9]{10}$/',
            'email' => 'required|string|max:20|unique:patrons,email,'
        ];
    
        $validator = Validator::make($request->all(), $rules);
    
        if ($validator->fails()) {
            return ApiController::respondWithError($validator->errors(), 422);
        }
    
        try {
            // Create a new patron instance
            $patron = Patron::create($request->all());
            
            // Return success response with the created patron resource
            return ApiController::respondWithSuccess(new PatronResource($patron), 'Patron created successfully', 201);
        } catch (\Exception $e) {
            // Return error response if an exception occurs
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patron $patron)
    {
        $rules = [
            'name' => 'required|string',
            'phone' => 'required|string|regex:/^[0-9]{10}$/',
            'email' => 'required|string|max:20|unique:patrons,email,' . $patron->id, 
        ];


        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ApiController::respondWithError($validator->errors(), 422);
        }

        $updated = $patron->update($request->all());
        
        return $updated
            ? ApiController::respondWithSuccess(new PatronResource($patron), 'Patron updated successfully', 200)
            : ApiController::respondWithServerError('Failed to update the patron.');
    
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // Find the patron by ID and delete it
            $patron = Patron::findOrFail($id);
            $patron->delete();
            
            // Return a success response with status code 204 (No Content)
            return ApiController::respondWithSuccess('Patron deleted successfuly', 204);
        } catch (\Exception $e) {
            // If the patron is not found, return a JSON response with a 404 status code
            if ($e instanceof ModelNotFoundException) {
                return ApiController::respondWithError('Patron not found', 404);
            }

            // For any other exception, return a JSON response with a 500 status code
            return ApiController::respondWithError('Internal Server Error', 500);
        }
    }
}
