<?php

use Carbon\Carbon;
use App\Libraries\agora\src\RtcTokenBuilder;
use App\Libraries\agora\src\RtmTokenBuilder;

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

if (! function_exists('agoraCallForToken')) {
    function agoraCallForToken($appID, $appCertificate, $channelName, $uid) {
    include(app_path() ."/Libraries/agora/src/RtcTokenBuilder.php");

    $appID = $appID;
    $appCertificate = $appCertificate;
    $channelName = $channelName;
    $uid = $uid;
    $uidStr = "2882341273";
    $role = RtcTokenBuilder::RoleAttendee;
    $expireTimeInSeconds = 3600;
    $currentTimestamp = (new DateTime("now", new DateTimeZone('UTC')))->getTimestamp();
    $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

    $token = RtcTokenBuilder::buildTokenWithUid($appID, $appCertificate, $channelName, $uid, $role, $privilegeExpiredTs);
    //echo 'Token with int uid: ' . $token . PHP_EOL;
    return $token;

    // $token = RtcTokenBuilder::buildTokenWithUserAccount($appID, $appCertificate, $channelName, $uidStr, $role, $privilegeExpiredTs);
    // echo 'Token with user account: ' . $token . PHP_EOL;
    }
}
if (! function_exists('agoraCallForRtmToken')) {

    function agoraCallForRtmToken($appID, $appCertificate, $uid) {
    include(app_path() ."/Libraries/agora/src/RtmTokenBuilder.php");

    $appID = $appID;
    $appCertificate = $appCertificate;
    $user = $uid;
    $role = RtmTokenBuilder::RoleRtmUser;
    $expireTimeInSeconds = 3600;
    $currentTimestamp = (new DateTime("now", new DateTimeZone('UTC')))->getTimestamp();
    $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

    $token = RtmTokenBuilder::buildToken($appID, $appCertificate, $user, $role, $privilegeExpiredTs);
    return $token;

    }
}
