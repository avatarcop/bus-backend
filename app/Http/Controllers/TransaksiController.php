<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;  
use App\Models\Transaksi; 
use Illuminate\Support\Facades\Auth; 
use Validator;
use Log;
use Illuminate\Support\Facades\Input;
use App\Models\MasterSetting;
use App\Http\Helpers\BusHelper;
use Hash;
use DB;

class TransaksiController extends Controller
{
    public function transaksilist(Request $request)
    {    
        try{
            $nama_api = 'transaksi list';
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
                            $data = Transaksi::orderBy('id', 'desc')->get();
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


    public function transaksinotifikasi(Request $request)
    {    
        try{
            $nama_api = 'transaksi notifikasi';
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
                            $data = Transaksi::with('masterpo', 'masterbus', 'customer')
                            ->select('id', 'bus_id', 'po_id', 'cust_id', 'created_at')
                            ->where('status', 0)
                            ->where('tgl_berangkat', '>', \Carbon\Carbon::now())
                            ->orderBy('id', 'desc')
                            ->get()
                            ->take(4);
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

    public function transaksicreate(Request $request)
    {    
        try{
            $nama_api = 'transaksi create';
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
                            $bus = \App\Models\Master_bus::with('masterpo', 'tipekursi')
                            ->where('status', 1)
                            ->whereDate('waktu_berangkat', '>', \Carbon\Carbon::now()->format('Y-m-d'))
                            ->orderBy('nama_bus', 'desc')
                            ->get();

                            if(count($bus) > 0)
                            {
                                $po = \App\Models\Master_po::where('status', 1)->orderBy('nama_po', 'desc')->get();
                                $tipekursi = \App\Models\Master_tipekursi::orderBy('nama_tipekursi', 'desc')->get();
                                $customer = \App\Models\Customer::where('status', 1)->orderBy('nama', 'desc')->get();
                                $result = [
                                    'status' => 1,
                                    'message' => 'Sukses',
                                    'data' => $po,
                                    'data_bus' => $bus,
                                    'data_tipekursi' => $tipekursi,
                                    'data_customer' => $customer,
                                ];    
                            }else{
                                $result = [
                                    'status' => 0,
                                    'message' => 'Tidak ada jadwal keberangkatan',
                                    'data' => []
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
            //::Info($nama_api.' sukses '.Input::get('email').' '.$_SERVER['REMOTE_ADDR']);
        }else{
            Log::Info($nama_api.' gagal '.Input::get('email').' '.$_SERVER['REMOTE_ADDR']);
        }

        // $update_log_awal = BusHelper::update_log($simpanlog_awal, json_encode($result), \Carbon\Carbon::now(), 'LogBus', $result['status']);

        return response()->json($result); 

    }

    public function transaksiedit(Request $request)
    {    
        try{
            $nama_api = 'transaksi edit';
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
                            $data = Transaksi::with('customer', 'masterpo', 'masterbus', 'masterbus.tipekursi')->where('id', $request->id)->first();
                            $po = \App\Models\Master_po::where('status', 1)->orderBy('nama_po', 'desc')->get();
                            $bus = \App\Models\Master_bus::with('masterpo', 'tipekursi')->where('status', 1)->orderBy('nama_bus', 'desc')->get();
                            $tipekursi = \App\Models\Master_tipekursi::orderBy('nama_tipekursi', 'desc')->get();
                            $customer = \App\Models\Customer::where('status', 1)->orderBy('nama', 'desc')->get();

                            $kursi_sisa = \App\Models\Master_bus::find($data->bus_id);

                            $result = [
                                'status' => 1,
                                'message' => 'Sukses',
                                'data' => $data,
                                'data_po' => $po,
                                'data_bus' => $bus,
                                'data_tipekursi' => $tipekursi,
                                'data_customer' => $customer,
                                'data_kursisisa' => $kursi_sisa->kursi_sisa,
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

    public function transaksiinsert(Request $request)
    {    
        try{
            $nama_api = 'transaksi insert';
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
                        $cek = Transaksi::where('email', Input::get('email'))->orderBy('id', 'desc')->first(); 
                        if($cek)
                        {
                            $result = [
                                'status' => 0,
                                'message' => 'Gagal, email sudah terdaftar'
                            ];
                        }else{
                            DB::beginTransaction();
                            $skrg = \Carbon\Carbon::now();
                            $po = \App\Models\Master_po::find($request->po_id);
                            $bus = \App\Models\Master_bus::find($request->bus_id);
                            $cust = \App\Models\Customer::find($request->cust_id);
                            $idtrx = $cust->id.$skrg->format('YmdHis');
                            
                            $cek_kursi = BusHelper::cekKursiAvailable();

                            if($cek_kursi['status'] == 1)
                            {
                                $simpan = new Transaksi;
                                $simpan->cust_id = $cust->id;
                                $simpan->po_id = $po->id;
                                $simpan->bus_id = $bus->id;
                                $simpan->nama_po = $po->nama_po;
                                $simpan->idtrx = $idtrx;
                                $simpan->email = $cust->email;
                                $simpan->no_hp = $request->no_hp;
                                $simpan->penumpang = $request->penumpang;
                                $simpan->kode_bus = $bus->kode_bus;
                                $simpan->nama_bus = $bus->nama_bus;
                                $simpan->terminal_berangkat = $bus->terminal_berangkat;
                                $simpan->tgl_berangkat = $bus->waktu_berangkat;
                                $simpan->nomor_kursi = $request->nomor_kursi;
                                $simpan->harga_tiket = $bus->harga_tiket;
                                $simpan->tgl_pesan = $skrg;
                                $simpan->tgl_update = $skrg;
                                $simpan->status = 0;
                                $simpan->trxmessage = 'Pemesanan tiket selesai, silahkan selesaikan pembayaran anda';
                                $simpan->save();

                                $result = [
                                    'status' => 1,
                                    'message' => 'Sukses',
                                    'data' => [],
                                ];

                                DB::commit();
                            }else{
                                $result = [
                                    'status' => 0,
                                    'message' => $cek_kursi['message'],
                                    'data' => [],
                                ];

                                DB::rollBack();
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
            Log::Info($nama_api.' gagal '.Input::get('email').' '.$_SERVER['REMOTE_ADDR'].' result '.json_encode($result));
        }

        $update_log_awal = BusHelper::update_log($simpanlog_awal, json_encode($result), \Carbon\Carbon::now(), 'LogBus', $result['status']);

        return response()->json($result); 

    } 

    public function transaksiupdate(Request $request)
    {   
        try{
            $nama_api = 'transaksi update';
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
                        DB::beginTransaction();
                        $skrg = \Carbon\Carbon::now();
                        $po = \App\Models\Master_po::find($request->po_id);
                        $bus = \App\Models\Master_bus::find($request->bus_id);
                        $cust = \App\Models\Customer::find($request->cust_id);
                        $idtrx = $cust->id.$skrg->format('YmdHis');

                        // REMOVE OLD BOOKNG
                        $booking = explode(",", $bus->kursi_booking);
                        $kursi_lama = explode(",", $request->nomor_kursi_lama);

                        $booking_fresh = array_diff($booking, $kursi_lama);
                        $booking_fresh = implode(',', $booking_fresh);
                        $bus->kursi_booking = $booking_fresh;
                        $bus->update();


                        // CEK KURSI
                        $cek_kursi = BusHelper::cekKursiAvailable();

                        if($cek_kursi['status'] == 1)
                        {
                            $update = Transaksi::find($request->id);
                            $update->cust_id = $cust->id;
                            $update->po_id = $po->id;
                            $update->bus_id = $bus->id;
                            $update->nama_po = $po->nama_po;
                            $update->idtrx = $idtrx;
                            $update->email = $cust->email;
                            $update->no_hp = $request->no_hp;
                            $update->penumpang = $request->penumpang;
                            $update->kode_bus = $bus->kode_bus;
                            $update->nama_bus = $bus->nama_bus;
                            $update->terminal_berangkat = $bus->terminal_berangkat;
                            $update->tgl_berangkat = $bus->waktu_berangkat;
                            $update->harga_tiket = $bus->harga_tiket;
                            $update->tgl_pesan = $skrg;
                            $update->tgl_update = $skrg;
                            $update->status = 0;
                            $update->trxmessage = 'Pemesanan tiket selesai, silahkan selesaikan pembayaran anda';
                            $update->update();

                            $result = [
                                'status' => 1,
                                'message' => 'Sukses',
                                'data' => [],
                            ];

                            DB::commit();
                        }else{
                            $result = [
                                'status' => 0,
                                'message' => $cek_kursi['message'],
                                'data' => [],
                            ];

                            DB::rollBack();
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

    public function transaksidelete(Request $request)
    {    
        try{
            $nama_api = 'transaksi delete';
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
                            DB::beginTransaction();
                            $data = Transaksi::find($request->id);
                            if($data)
                            {
                                $data->delete();
                                $result = [
                                    'status' => 1,
                                    'message' => 'Sukses',
                                    'data' => []
                                ];

                                DB::commit();
                            }else{
                                $result = [
                                    'status' => 0,
                                    'message' => 'Gagal, data tidak ditemukan'
                                ];
                                DB::rollBack();
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

    public function transaksibayar(Request $request)
    {    
        try{
            $nama_api = 'transaksi bayar';
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
                            DB::beginTransaction();
                            $data = Transaksi::with('customer')->find($request->id);
                            if($data)
                            {
                                $data->status=1;
                                $data->update();

                                // UPDATE KURSI BOOKING, SISA, DAN TERJUAL
                                $bus = \App\Models\Master_bus::find($data->bus_id);
                                $nomor_kursi = explode(',', $data->nomor_kursi);
                                $kursi_booking = explode(',', $bus->kursi_booking);
                                $kursi_terjual = explode(',', $bus->kursi_terjual);
                                
                                $booking = array_diff($kursi_booking, $nomor_kursi);
                                $booking = implode(',', $booking);

                                $terjual = array_merge($kursi_terjual, $nomor_kursi);
                                $terjual = implode(',', $terjual);

                                if(substr($terjual, 0,1) == ',')
                                {
                                    $terjual = substr($terjual, 1);
                                }
                                $bus->kursi_booking = $booking;
                                $bus->kursi_terjual = $terjual;
                                $bus->update();

                                $result = [
                                    'status' => 1,
                                    'message' => 'Status pembayaran trx id : '.$data->idtrx.' a/n '.$data->customer->nama.' berhasil diapprove',
                                    'data' => []
                                ];

                                DB::commit();
                            }else{
                                $result = [
                                    'status' => 0,
                                    'message' => 'Gagal, data tidak ditemukan'
                                ];

                                DB::rollBack();
        
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
