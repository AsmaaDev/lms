<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class ApiController extends Controller
{
   
    public static function createUserDeviceToken($userId, $deviceToken, $deviceType)
    {
        $created = UserDevice::create(['user_id' => $userId, 'device_type' => $deviceType, 'device_token' => $deviceToken]);

        return $created;
    }

    public static function respondWithSuccess($data)
    {
        return response()->json(['responseCode' => 1, 'status_Code' => 200, 'data' => $data]);
    }

    public static function respondWithError($errors)
    {
        return response()->json(['responseCode' => 0, 'status_Code' => 422, 'errors' => $errors]);
    }

    public static function respondWithNotFound()
    {
        return response()->json(['responseCode' => 0, 'status_Code' => 404, 'error' => 'Not found']);
    }

    public static function respondWithServerError()
    {
        return response()->json(['responseCode' => 0, 'status_Code' => 500]);
    }

   
}
