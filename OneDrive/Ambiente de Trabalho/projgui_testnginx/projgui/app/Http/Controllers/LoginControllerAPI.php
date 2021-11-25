<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use GuzzleHttp\Exception\ConnectException;

define('YOUR_SERVER_URL', 'http://sail-project-1.test:87');
//define('YOUR_SERVER_URL', 'apache2');
//192.168.1.79
//sail-project-1.test
// Check "oauth_clients" table for next 2 values:
define('CLIENT_ID', '2');
define('CLIENT_SECRET', 'dlVMgZQoW4VlNLLAfBWTOhjg275UEYh2iqMzATQ9');

class LoginControllerAPI extends Controller
{
    public function login(Request $request)
    {
        $http = new \GuzzleHttp\Client;
        $url = YOUR_SERVER_URL . '/oauth/token';
        $bodyHttpRequest = [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => CLIENT_ID,
                'client_secret' => CLIENT_SECRET,
                'username' => $request->input('email'),
                'password' => $request->input('password'),
                'scope' => ''
            ],
            'exceptions' => false,
            
        ];
        $response = $http->post($url,$bodyHttpRequest);
        $errorCode = $response->getStatusCode();
        if ($errorCode == '200') {
            return json_decode((string) $response->getBody(), true);
        } else {
            return response()->json(
                ['msg' =>"Credencias Invalidas"],
                $errorCode
            );
        }
    }
    
    public function logout()
    {
        \Auth::guard('api')->user()->token()->revoke();
        \Auth::guard('api')->user()->token()->delete();
        return response()->json(['msg'=>'Token revoked'], 200);
    }
}
