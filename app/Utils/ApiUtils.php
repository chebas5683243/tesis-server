<?php

namespace App\Utils;

use Illuminate\Http\Response;
use App\Utils\ApiConsts;

class ApiUtils {
    public static function respuesta($isSuccessful, $data = null) {
        $response = [];

        if ($isSuccessful) {
                $response['status'] = ApiConsts::SUCCESS;
                $response['data']  = $data;
                $statusCode = 200;
        } else {
            if ($data) {
                $response['status'] = ApiConsts::FAIL;
                $response['data'] = $data;
                $statusCode = 400;
            } else {
                $response['status'] = ApiConsts::ERROR;
                $response['message'] = 'Internal Server Error.';
                $statusCode = 500;
            }
        } 

        return response()->json($response, $statusCode);
    }
}
