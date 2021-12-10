<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; 
use App\Models\User; 
use App\Models\Accesscontrol; 
use App\Models\Master_po; 
use Illuminate\Support\Facades\Auth; 
use Validator;
use Log;
use Illuminate\Support\Facades\Input;
use App\Models\MasterSetting;
use App\Http\Helpers\BusHelper;

class MasterpoController extends Controller
{
    public function polist(Request $request)
    {    
        try{
            $nama_api = 'po list';
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
                            $data = \App\Models\Master_po::orderBy('id', 'desc')->get();
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
            //Log::Info($nama_api.' sukses '.Input::get('email').' '.$_SERVER['REMOTE_ADDR']);
        }else{
            Log::Info($nama_api.' gagal '.Input::get('email').' '.$_SERVER['REMOTE_ADDR']);
        }

        

        // $update_log_awal = BusHelper::update_log($simpanlog_awal, json_encode($result), \Carbon\Carbon::now(), 'LogBus', $result['status']);

        return response()->json($result); 

    } 

    public function pocreate(Request $request)
    {    
        try{
            $nama_api = 'po create';
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

    public function poedit(Request $request)
    {    
        try{
            $nama_api = 'po edit';
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
                            $user = \App\Models\Master_po::find($request->id);
                            if($user)
                            {
                                $result = [
                                    'status' => 1,
                                    'message' => 'Sukses',
                                    'data' => $user
                                ];
                            }else{
                                $result = [
                                    'status' => 0,
                                    'message' => 'Gagal'
                                ];

                                Log::Info($nama_api.' data not dound '.Input::get('email').' '.$_SERVER['REMOTE_ADDR']);
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

        

        // $update_log_awal = BusHelper::update_log($simpanlog_awal, json_encode($result), \Carbon\Carbon::now(), 'LogBus', $result['status']);

        return response()->json($result); 

    }

    public function poinsert(Request $request)
    {    
        try{
            $nama_api = 'po insert';
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
                        $cek = \App\Models\Master_po::where('logo_po', Input::get('logo_po'))->first();
                        if($cek)
                        {
                            $result = [
                                    'status' => 0,
                                    'message' => 'Gagal, nama file sudah ada, silahkan rubah nama file anda'
                                ];
                        }else{
                            $simpan = new \App\Models\Master_po;
                            $simpan->logo_po = Input::get('logo_po');
                            $simpan->nama_po = Input::get('nama_po');
                            $simpan->status = Input::get('status');
                            $simpan->save();

                            $result = [
                                'status' => 1,
                                'message' => 'Sukses'
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


    public function poupdate(Request $request)
    {    
        try{
            $nama_api = 'po update';
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
                        $update = \App\Models\Master_po::find($request->id);
                        if(Input::get('logo_po'))
                        {
                            if($update->logo_po != Input::get('logo_po'))
                            {
                                $update->logo_po = Input::get('logo_po');
                                $update->nama_po = Input::get('nama_po');
                                $update->status = Input::get('status');
                                $update->update();

                                $result = [
                                    'status' => 1,
                                    'message' => 'Sukses',
                                    'data' => $update,
                                ];
                            }else{
                                $result = [
                                    'status' => 0,
                                    'message' => 'Gagal, nama file sudah ada, silahkan rubah nama file anda'
                                ];
                            }
                        }else{
                            $update->nama_po = Input::get('nama_po');
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

    public function podelete(Request $request)
    {    
        try{
            $nama_api = 'po delete';
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
                    
                            $data = \App\Models\Master_po::find($request->id);
                            if($data)
                            {
                                $old_image = $data->logo_po;
                                $data->delete();
                                $result = [
                                    'status' => 1,
                                    'message' => 'Sukses',
                                    'data' => $old_image
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
