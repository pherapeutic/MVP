<?php

use Carbon\Carbon;

if (! function_exists('currentDateTime')) {
    function currentDateTime() {
        return Carbon::now()->toDateTimeString();
    }
}

if (! function_exists('frontendDateTimeFormat')) {
    function frontendDateTimeFormat($format = 'Y-m-d H:i', $date = '', $timeZone = '') {
        if($date){
            $timeZone = ($timeZone) ? ($timeZone) : (env('APP_TIMEZONE'));
            return Carbon::parse($date)->timeZone($timeZone)->format($format);
        }
        return $date;
    }
}

if (! function_exists('returnNotFoundResponse')) {
    function returnNotFoundResponse($message = '', $data = array()) {
        $returnArr = [
            'statusCode' => 404,
            'status' => 'not found',
            'message' => $message,
            'data' => ($data) ? ($data) : ((object) $data)
        ];
        return response()->json($returnArr, 404);
    }
}

if (! function_exists('returnValidationErrorResponse')) {
    function returnValidationErrorResponse($message = '', $data = array()) {
        $returnArr = [
            'statusCode' => 422,
            'status' => 'vaidation error',
            'message' => $message,
            'data' => ($data) ? ($data) : ((object) $data)
        ];
        return response()->json($returnArr, 422);
    }
}

if (! function_exists('returnSuccessResponse')) {
    function returnSuccessResponse($message = '', $data = array()) {
        $returnArr = [
            'statusCode' => 200,
            'status' => 'success',
            'message' => $message,
            'data' => ($data) ? ($data) : ((object) $data)
        ];
        return response()->json($returnArr, 200);
    }
}

if (! function_exists('returnErrorResponse')) {
    function returnErrorResponse($message = '', $data = array()) {
        $returnArr = [
            'statusCode' => 500,
            'status' => 'error',
            'message' => $message,
            'data' => ($data) ? ($data) : ((object) $data)
        ];
        return response()->json($returnArr, 500);
    }
}

if (! function_exists('getLanguages')) {
    function getLanguages() {
        $languagesArr = \App\Models\Language::getLanguagesDropdownArr();
        return $languagesArr;
    }
}

if (! function_exists('getTherapistTypes')) {
    function getTherapistTypes() {
        $therapistTypesArr = \App\Models\TherapistType::getTherapistTypesDropdownArr();
        return $therapistTypesArr;
    }
}
