<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;  
use App\Models\Customer; 
use Illuminate\Support\Facades\Auth; 
use Validator;
use Log;
use Illuminate\Support\Facades\Input;
use App\Models\MasterSetting;
use App\Http\Helpers\BusHelper;
use Hash;

class CustomerController extends Controller
{
    public function customerlist(Request $request)
    {    
        try{
            $nama_api = 'customer list';
            $ket_validator = 'validator';

            // Simpan log
            // $simpanlog_awal = BusHelper::simpan_log($nama_api,$request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'frontend', $_SERVER['REMOTE_ADDR'], 'LogBus');

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
                $result= BusHelper::cek_signature();   
                if($result != 1){
    
                    $result = [
                        'status' => 0,
                        'message' => 'Duplikat signature'
                    ];
                }else
                {
                    $result= BusHelper::dekrip_signature();   


                    if ($result['status'] != 1)      
                    {
                        // gagal signature
                    }else{
                        // cek akses route
                        $result= BusHelper::cekAksesRoute();
                        if($result['status'] != 1)
                        {
                            // tidak berhak akses
                        }else{
                            $data = Customer::orderBy('id', 'desc')->get();
                            $result = [
                                'status' => 1,
                                'message' => 'Sukses',
                                'data' => $data
                            ];
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
           // Log::Info($nama_api.' sukses '.Input::get('email').' '.$_SERVER['REMOTE_ADDR']);
        }else{
            Log::Info($nama_api.' gagal '.Input::get('email').' '.$_SERVER['REMOTE_ADDR']);
        }

        

        // $update_log_awal = BusHelper::update_log($simpanlog_awal, json_encode($result), \Carbon\Carbon::now(), 'LogBus', $result['status']);

        return response()->json($result); 

    } 

    public function customercreate(Request $request)
    {    
        try{
            $nama_api = 'customer create';
            $ket_validator = 'validator';
            $ket_db = 'db';

            // Simpan log
            // $simpanlog_awal = BusHelper::simpan_log($nama_api,$request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'frontend', $_SERVER['REMOTE_ADDR'], 'LogBus');

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
                $result= BusHelper::cek_signature();   
                if($result != 1){
    
                    $result = [
                        'status' => 0,
                        'message' => 'Duplikat signature'
                    ];
                }else
                {
                    $result= BusHelper::dekrip_signature();   


                    if ($result['status'] != 1)      
                    {
                        // gagal signature
                    }else{
                        // cek akses route
                        $result= BusHelper::cekAksesRoute();
                        if($result['status'] != 1)
                        {
                            // tidak berhak akses
                        }else{
                            $result = [
                                'status' => 1,
                                'message' => 'Sukses',
                                'data' => []
                            ];
                           
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

        

        // $update_log_awal = BusHelper::update_log($simpanlog_awal, json_encode($result), \Carbon\Carbon::now(), 'LogBus', $result['status']);

        return response()->json($result); 

    }

    public function customeredit(Request $request)
    {    
        try{
            $nama_api = 'customer edit';
            $ket_validator = 'validator';
            $ket_db = 'db';

            // Simpan log
            // $simpanlog_awal = BusHelper::simpan_log($nama_api,$request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'frontend', $_SERVER['REMOTE_ADDR'], 'LogBus');

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
                $result= BusHelper::cek_signature();   
                if($result != 1){
    
                    $result = [
                        'status' => 0,
                        'message' => 'Duplikat signature'
                    ];
                }else
                {
                    $result= BusHelper::dekrip_signature();   


                    if ($result['status'] != 1)      
                    {
                        // gagal signature
                    }else{
                        // cek akses route
                        $result= BusHelper::cekAksesRoute();
                        if($result['status'] != 1)
                        {
                            // tidak berhak akses
                        }else{
                            $data = Customer::find($request->id);
                            $result = [
                                'status' => 1,
                                'message' => 'Sukses',
                                'data' => $data
                            ];
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

        

        // $update_log_awal = BusHelper::update_log($simpanlog_awal, json_encode($result), \Carbon\Carbon::now(), 'LogBus', $result['status']);

        return response()->json($result); 

    }

    public function customerinsert(Request $request)
    {    
        try{
            $nama_api = 'customer insert';
            $ket_validator = 'validator';
            $ket_db = 'db';

            // Simpan log
            $simpanlog_awal = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'frontend', $_SERVER['REMOTE_ADDR'], 'LogBus');

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
                $result= BusHelper::cek_signature();   
                if($result != 1){
    
                    $result = [
                        'status' => 0,
                        'message' => 'Duplikat signature'
                    ];
                }else
                {
                    $result= BusHelper::dekrip_signature();   

                    if ($result['status'] != 1)      
                    {
                        // gagal signature
                    }else{
                        $cek = Customer::where('email', Input::get('email'))->orderBy('id', 'desc')->first(); 
                        if($cek)
                        {
                            $result = [
                                'status' => 0,
                                'message' => 'Gagal, email sudah terdaftar'
                            ];
                        }else{
                            $simpan = new Customer;
                            $simpan->nama = Input::get('nama');
                            $simpan->email = Input::get('email');
                            $simpan->password = Hash::make(Input::get('password'));
                            $simpan->no_hp = Input::get('no_hp');
                            $simpan->status = Input::get('status');
                            $simpan->save();

                            $result = [
                                'status' => 1,
                                'message' => 'Sukses',
                                'data' => [],
                            ];
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
            Log::Info($nama_api.' gagal '.Input::get('email').' '.$_SERVER['REMOTE_ADDR'].' result '.json_encode($result));
        }

        

        $update_log_awal = BusHelper::update_log($simpanlog_awal, json_encode($result), \Carbon\Carbon::now(), 'LogBus', $result['status']);

        return response()->json($result); 

    } 

    public function customerupdate(Request $request)
    {   
        try{
            $nama_api = 'customer update';
            $ket_validator = 'validator';
            $ket_db = 'db';

            // Simpan log
            $simpanlog_awal = BusHelper::simpan_log($nama_api,$request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'frontend', $_SERVER['REMOTE_ADDR'], 'LogBus');

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
                $result= BusHelper::cek_signature();   
                if($result != 1){
    
                    $result = [
                        'status' => 0,
                        'message' => 'Duplikat signature'
                    ];
                }else
                {
                    $result= BusHelper::dekrip_signature();   

                    if ($result['status'] != 1)      
                    {
                        // gagal signature
                    }else{
                        

                        $update = Customer::find($request->id);
                        if(Hash::check(Input::get('password'), $update->password))
                        {   
                            // not change password 
                            Log::Info('pwd sama');
                        }else{
                            Log::Info('pwd beda');
                            $update->password = Hash::make(Input::get('password'));
                        }

                        $cek = Customer::where('email', Input::get('email'))
                            ->where('id', '!=', $request->id)
                            ->orderBy('id', 'desc')
                            ->first();

                        if($cek)
                        {
                            $result = [
                                'status' => 0,
                                'message' => 'Gagal, email sudah terdaftar',
                               
                            ];
                        }else{
                            $update->email = Input::get('email');
                            $update->nama = Input::get('nama');
                            $update->no_hp = Input::get('no_hp');
                            $update->status = Input::get('status');
                            $update->update();

                            $result = [
                                'status' => 1,
                                'message' => 'Sukses',
                                'data' => $update,
                            ];
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

        

        $update_log_awal = BusHelper::update_log($simpanlog_awal, json_encode($result), \Carbon\Carbon::now(), 'LogBus', $result['status']);

        return response()->json($result); 

    }

    public function customerdelete(Request $request)
    {    
        try{
            $nama_api = 'customer delete';
            $ket_validator = 'validator';
            $ket_db = 'db';

            // Simpan log
            $simpanlog_awal = BusHelper::simpan_log($nama_api,$request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'frontend', $_SERVER['REMOTE_ADDR'], 'LogBus');

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
                $result= BusHelper::cek_signature();   
                if($result != 1){
    
                    $result = [
                        'status' => 0,
                        'message' => 'Duplikat signature'
                    ];
                }else
                {
                    $result= BusHelper::dekrip_signature();   


                    if ($result['status'] != 1)      
                    {
                        // gagal signature
                    }else{
                        // cek akses route
                        $result= BusHelper::cekAksesRoute();
                        if($result['status'] != 1)
                        {
                            // tidak berhak akses
                        }else{
                    
                            $data = Customer::find($request->id);
                            if($data)
                            {
                                $data->delete();
                                $result = [
                                    'status' => 1,
                                    'message' => 'Sukses',
                                    'data' => []
                                ];
                            }else{
                                $result = [
                                    'status' => 0,
                                    'message' => 'Gagal, data tidak ditemukan'
                                ];
        
                            }
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

        

        $update_log_awal = BusHelper::update_log($simpanlog_awal, json_encode($result), \Carbon\Carbon::now(), 'LogBus', $result['status']);

        return response()->json($result); 

    } 
	

   
}
