<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

trait ApiResponser
{
    protected function successResponse($data, $code = 200, $token = null)
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'access_token' => $token
        ], $code);
    }

    protected function errorResponse($message, $code)
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], $code);
    }

    protected function showAll(Collection $collection, $code = 200)
    {
        return $this->successResponse($collection, $code);
    }

    protected function showOne(Model $model, $code = 200)
    {
        return $this->successResponse($model, $code);
    }
}
