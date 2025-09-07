<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function successResponse($data, $message = '', $statusCode = 200)
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
        ], $statusCode);
    }

    public function errorResponse($message, $statusCode = 400)
    {
        return response()->json([
            'message' => $message,
        ], $statusCode);
    }
}
