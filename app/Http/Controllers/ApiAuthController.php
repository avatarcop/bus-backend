<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; 
use App\Models\User; 
use App\Models\Accesscontrol; 
use Illuminate\Support\Facades\Auth; 
use Validator;
use Log;
use Illuminate\Support\Facades\Input;
use App\Models\MasterSetting;
use App\Http\Helpers\BusHelper;

class ApiAuthController extends Controller
{
	public function login(Request $request)
    {    
        try{
            $nama_api = 'login';
            $ket_validator = 'validator';
            $ket_db = 'db';

            // Simpan log
            $simpanlog_awal = BusHelper::simpan_log($nama_api,$request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'frontend', $_SERVER['REMOTE_ADDR'], 'LogUser');

            $ip = BusHelper::setting('access_allow_ip');
            $ip = explode(",", $ip);
            if(!in_array($_SERVER['REMOTE_ADDR'], $ip))
            {
                $simpanlog_ip = BusHelper::simpanlog_ip($nama_api, $_SERVER['REMOTE_ADDR'], json_encode($request->all()), $request_time, $ket_validator, Input::get('email'));
                $result = [
                                    'status' =>  0,
                                    'message' => 'IP tidak dikenal '.$_SERVER['REMOTE_ADDR'],
                                    'data' => $_SERVER['REMOTE_ADDR']
                                ];
            }else
            {
                
            	if(Auth::attempt(['email' => request('email'), 'password' => request('password')]))
				{ 
				   $user = Auth::user(); 
                   $acl = Accesscontrol::find($user->accesscontrol_id); 
				   $token =  $user->createToken('AppName')->accessToken;
				   $iv = BusHelper::setting('iv');
				   $key = BusHelper::setting('key');
				   $result = [
				   		'status' => 1,
				   		'message' => 'Sukses',
				   		'data' => [
				   			'user' => $user,
                            'acl' => $acl,
				   			'token' => $token,
				   			'iv' => $iv,
				   			'key' => $key,
				   		]
				   ]; 

			  	}else{ 
				  	 $result = [
					   		'status' => 0,
					   		'message' => 'Unauthorized'
					   ]; 
			   	} 


                
            }

        }catch (\Exception $e) {
            $result = [
                'status' => 0,
                'message' => 'Error exception',
                'error' => report($e),
            ];
            Log::Info($nama_api.' error exception '.Input::get('email').' '.$_SERVER['REMOTE_ADDR'].' '.json_encode($result));


        }catch (\Throwable $e) {
            $result = [
                'status' => 0,
                'message' => 'Error throwable',
                'error' => report($e),
            ];

            Log::Info($nama_api.' error Throwable '.Input::get('email').' '.$_SERVER['REMOTE_ADDR'].' '.json_encode($result));
        }

        if($result['status'] == 1)
        {
        	Log::Info($nama_api.' sukses '.Input::get('email').' '.$_SERVER['REMOTE_ADDR']);
        }else{
        	Log::Info($nama_api.' gagal '.Input::get('email').' '.$_SERVER['REMOTE_ADDR']);
        }

        $update_log_awal = BusHelper::update_log($simpanlog_awal, json_encode($result), \Carbon\Carbon::now(), 'LogUser', $result['status']);

        return response()->json($result); 

    } 

    public function logout(Request $request)
    {    
        try{
            $nama_api = 'logout';
            $ket_validator = 'validator';
            $ket_db = 'db';

            // Simpan log
            $simpanlog_awal = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'frontend', $_SERVER['REMOTE_ADDR'], 'LogUser');

            $ip = BusHelper::setting('access_allow_ip');
            $ip = explode(",", $ip);
            if(!in_array($_SERVER['REMOTE_ADDR'], $ip))
            {
                $simpanlog_ip = BusHelper::simpanlog_ip($nama_api, $_SERVER['REMOTE_ADDR'], json_encode($request->all()), $request_time, $ket_validator, Input::get('email'));
                $result = [
                                    'status' =>  0,
                                    'message' => 'IP tidak dikenal '.$_SERVER['REMOTE_ADDR'],
                                    'data' => $_SERVER['REMOTE_ADDR']
                                ];
            }else
            {
                // cek signature  
                $result= BusHelper::cek_signature($request->backend_signature);   
                if($result != 1){
    
                    $result = [
                        'status' => 0,
                        'message' => 'Duplikat signature'
                    ];
                }else
                {
                    $result= BusHelper::dekrip_signature($request->backend_signature);   

                    if ($result['status'] != 1)      
                    {
                        // gagal signature
                    }else{

                        $user = \App\Models\User::where('email', $request->email)->first();
                        if($user)
                        {
                            $token = \App\Models\OauthAccessTokens::where('user_id', $user->id)->get();
                            foreach ($token as $key) {
                                $tokenDel = \App\Models\OauthAccessTokens::where('user_id', $key->user_id)->first();
                                $tokenDel->delete();
                            }

                            Log::Info('token deleted logout');

                            $result = [
                                'status' => 1,
                                'message' => 'Sukses',
                                'data' => '-',
                            ];
                        }else{
                            $result = [
                                'status' => 0,
                                'message' => 'Gagal',
                            ];

                            Log::Info('gagal logout');
                        }
                        

                    }


                }
                

                
            }

        }catch (\Exception $e) {
            $result = [
                'status' => 0,
                'message' => 'Error exception',
                'error' => report($e),
            ];
            Log::Info($nama_api.' error exception '.Input::get('email').' '.$_SERVER['REMOTE_ADDR'].' '.json_encode($result));


        }catch (\Throwable $e) {
            $result = [
                'status' => 0,
                'message' => 'Error throwable',
                'error' => report($e),
            ];

            Log::Info($nama_api.' error Throwable '.Input::get('email').' '.$_SERVER['REMOTE_ADDR'].' '.json_encode($result));
        }

        if($result['status'] == 1)
        {
            Log::Info($nama_api.' sukses '.Input::get('email').' '.$_SERVER['REMOTE_ADDR']);
        }else{
            Log::Info($nama_api.' gagal '.Input::get('email').' '.$_SERVER['REMOTE_ADDR']);
        }

        

        $update_log_awal = BusHelper::update_log($simpanlog_awal, json_encode($result), \Carbon\Carbon::now(), 'LogUser', $result['status']);

        return response()->json($result); 

    } 


	public function getUser() 
	{
		 $user = Auth::user();
		 return response()->json(['success' => $user]); 
	}
}
