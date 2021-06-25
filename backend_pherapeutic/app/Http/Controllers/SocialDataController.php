<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\user;

class SocialDataController extends Controller
{

    public function dataDeletionCallback(Request $request)
    {

        $signed_request = $request->input('signed_request');
        $data = $this->parse_signed_request($signed_request);
        $user_id = $data['user_id'];

        // here will delete the user base on the user_id from facebook
        User::where( ['social_token' => $user_id])->delete();

        // here will check if the user is deleted
        $isDeleted = User::where(['social_token' => $user_id])->first();

        if ($isDeleted ===null) {
            return response(json_encode([
                'url' => url("deletion_status/{$user_id}"),
                'code' => $user_id,
            ], JSON_UNESCAPED_SLASHES))->header('Content-Type', "application/json");
        }

        return response()->json([
            'message' => 'operation not successful'
        ], 500);
    }

    private function parse_signed_request($signed_request) {
        list($encoded_sig, $payload) = explode('.', $signed_request, 2);

        $secret = '6c63061fe2f3b3bcf1be458b2c7e703e'; // Use your app secret here

        // decode the data
        $sig = $this->base64_url_decode($encoded_sig);
        $data = json_decode($this->base64_url_decode($payload), true);

        // confirm the signature
        $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
        if ($sig !== $expected_sig) {
            error_log('Bad Signed JSON signature!');
            return null;
        }

        return $data;
    }

    private function base64_url_decode($input) {
        return base64_decode(strtr($input, '-_', '+/'));
    }



    public function deletionStatus($id){

        $isDeleted = User::where(['social_token' => $id])->first();

        if ($isDeleted ===null) {
            return 'data deleted';
        }else{
            return 'data not deleted';
        }
    }
}
