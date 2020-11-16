<?php

namespace App\Traits;

trait HTTPResponseTrait
{
    /**
     * Success response message
     *
     * @param $message message
     * @param $code response code
     */
    public function successResponse(string $message, int $code = 200, $data = null)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Error response message
     *
     * @param $message message
     * @param $code response code
     */
    public function errorResponse(string $message, int $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $code);
    }
}
