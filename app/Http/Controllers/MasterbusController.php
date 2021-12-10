<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;  
use App\Models\Master_bus; 
use Illuminate\Support\Facades\Auth; 
use Validator;
use Log;
use Illuminate\Support\Facades\Input;
use App\Models\MasterSetting;
use App\Http\Helpers\BusHelper;

class MasterbusController extends Controller
{
    public function buslist(Request $request)
    {    
        try{
            $nama_api = 'bus list';
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
                            $data = \App\Models\Master_bus::with('masterpo')->orderBy('id', 'desc')->get();
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

    public function buscreate(Request $request)
    {    
        try{
            $nama_api = 'bus create';
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
                            $data = \App\Models\Master_po::where('status', 1)->get();
                            $data_tipekursi = \App\Models\Master_tipekursi::orderBy('nama_tipekursi', 'desc')->get();
                            $result = [
                                'status' => 1,
                                'message' => 'Sukses',
                                'data' => $data,
                                'data_tipekursi' => $data_tipekursi,
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

    public function busedit(Request $request)
    {    
        try{
            $nama_api = 'bus edit';
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
                            $user = \App\Models\Master_bus::find($request->id);
                            $data_po = \App\Models\Master_po::where('status', 1)->get();
                            $data_tipekursi = \App\Models\Master_tipekursi::orderBy('nama_tipekursi', 'desc')->get();
                            if($user)
                            {
                                $result = [
                                    'status' => 1,
                                    'message' => 'Sukses',
                                    'data' => $user,
                                    'data_po' => $data_po,
                                    'data_tipekursi' => $data_tipekursi,
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

    public function businsert(Request $request)
    {    
        try{
            $nama_api = 'bus insert';
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

                        $kursi_tersedia='';
                        for ($i=1; $i <= Input::get('jumlah_kursi') ; $i++) 
                        { 
                            if($kursi_tersedia=='')
                            {
                                $kursi_tersedia = $i;    
                            }else{
                                $kursi_tersedia = $kursi_tersedia.','.$i;
                            }
                        }
                        $simpan = new \App\Models\Master_bus;
                        $simpan->po_id = Input::get('po_id');
                        $simpan->nama_bus = Input::get('nama_bus');
                        $simpan->tipekursi_id = Input::get('tipekursi_id');
                        $simpan->terminal_berangkat = Input::get('terminal_berangkat');
                        $simpan->terminal_tujuan = Input::get('terminal_tujuan');
                        $simpan->jumlah_kursi = Input::get('jumlah_kursi');
                        $simpan->kursi_tersedia = $kursi_tersedia;
                        $simpan->kursi_sisa = $kursi_tersedia;
                        $simpan->harga_tiket = Input::get('harga_tiket');
                        $simpan->waktu_berangkat = Input::get('waktu_berangkat');
                        $simpan->estimasi_tiba = Input::get('estimasi_tiba');
                        $simpan->status = Input::get('status');
                        $simpan->save();

                        $kd = \App\Models\Master_bus::find($simpan->id);
                        $kd->kode_bus = 'B'.$simpan->po_id.'-'.$simpan->id;
                        $kd->update();

                        $result = [
                            'status' => 1,
                            'message' => 'Sukses',
                        ];
                        
                        
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

    public function busupdate(Request $request)
    {    
        try{
            $nama_api = 'bus update';
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
                        $kursi_tersedia='';
                        for ($i=1; $i <= Input::get('jumlah_kursi') ; $i++) 
                        { 
                            if($kursi_tersedia=='')
                            {
                                $kursi_tersedia = $i;    
                            }else{
                                $kursi_tersedia = $kursi_tersedia.','.$i;
                            }
                        }

                        $update = \App\Models\Master_bus::find($request->id);
                        $update->po_id = Input::get('po_id');
                        $update->nama_bus = Input::get('nama_bus');
                        $update->tipekursi_id = Input::get('tipekursi_id');
                        $update->terminal_berangkat = Input::get('terminal_berangkat');
                        $update->terminal_tujuan = Input::get('terminal_tujuan');
                        $update->jumlah_kursi = Input::get('jumlah_kursi');
                        $update->kursi_tersedia = $kursi_tersedia;
                        if($update->kursi_booking == '' && $update->kursi_terjual == '')
                        {
                            $update->kursi_sisa = $kursi_tersedia;
                        }
                        $update->harga_tiket = Input::get('harga_tiket');
                        $update->waktu_berangkat = Input::get('waktu_berangkat');
                        $update->estimasi_tiba = Input::get('estimasi_tiba');
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

    public function busdelete(Request $request)
    {    
        try{
            $nama_api = 'bus delete';
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
                    
                            $data = \App\Models\Master_bus::find($request->id);
                            if($data)
                            {
                                $data->delete();
                                $result = [
                                    'status' => 1,
                                    'message' => 'Sukses'
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
