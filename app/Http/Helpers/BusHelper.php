<?php

namespace App\Http\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use Log;
use App\Models\MasterSetting;
class BusHelper extends Controller
{
	public static function simpan_log($nama_api='', $user='',$request=[],$request_datetime='',$ket='',$ip='',$table_log='')
    {

        $log='';
        if( $table_log == 'LogUser')
        {
            $log = new \App\Models\LogUser;
        }elseif( $table_log == 'LogBus')
        {
            $log = new \App\Models\LogBus;
        }

        $log->api = $nama_api;
        $log->user = $user;
        $log->request = $request;
        $log->request_datetime = $request_datetime;
        $log->keterangan = $ket;
        $log->ipaddress = $ip;
        $log->save();

        return $log->id;
    }

    public static function update_log($log_id='', $result=[], $response_time='',$table_log='', $status='')
    {

        $log='';
        if( $table_log == 'LogUser')
        {
            $log = \App\Models\LogUser::find($log_id);
        }elseif( $table_log == 'LogBus')
        {
            $log = \App\Models\LogBus::find($log_id);
        }

        $log->response = $result;
        $log->response_datetime = $response_time;
        $log->status = $status;
        $log->update();

        return 1;
    }

    public static function enable_all_service()
    {
        $cekservis       = MasterSetting::where('setting_name', 'enable_all_service')->first();
        $cekservis_msg   = MasterSetting::where('setting_name', 'enable_all_service_message')->first();
        if($cekservis && $cekservis_msg)
        {
            if($cekservis->setting_value == 1)
            {
                $result = [
                    'status' => 1,
                    'message' => 'Sukses',
                ];

            }else{
                $result = [
                    'status' => 0,
                    'message' => $cekservis_msg->setting_value,
                ];

                Log::Info('Disable all service');
            }
        }else{
            $result = [
                    'status' => 0,
                    'message' => 'Maintenance',
                ];

            Log::Info('Disable all service, master setting is not correct');
        }

        return $result;
    }

    public static function check_blacklist()
    {
        $cek_blacklist = \App\Models\Blacklist::select('id', 'user', 'ket', 'created_at', 'updated_at')
            ->where('user', Input::get('email'))
            ->first();

        if(isset($cek_blacklist))
        {
            Log::Info('Error blacklist '.Input::get('email').' - '.json_encode($cek_blacklist));

            return [
                    'status' => 0,
                    'message' => 'User '.Input::get('email').' tidak diizinkan untuk menggunakan aplikasi ini',
            ];
        }else{
                return [
                        'status' => 1,
                        'message' => 'Sukses',

                    ];

        }
    }

    public static function setting($setting_name='')
    {
        $setting = MasterSetting::where('setting_name', $setting_name)->first();
        if($setting)
        {
            return $setting->setting_value;
        }else{
            return '';
        }

    }

    public static function simpanlog_ip($nama_api='', $ip='', $request='', $request_datetime='', $ket='', $user='')
    {
        $simpanlog = new \App\Models\LogIp;
        $simpanlog->api = $nama_api;
        $simpanlog->ip = $ip;
        $simpanlog->request = $request;
        $simpanlog->request_datetime = $request_datetime;
        $simpanlog->keterangan = $ket;
        $simpanlog->reseller_id = $user;
        $simpanlog->save();

        return $simpanlog;
    }

    public static function cek_signature() 
    {
        $tes = BusHelper::setting('signature_testing');
        if($tes == 1)
        {
            Log::Info('Signature testing backend');
            $result = 1;    
        }else{
            $signat = \App\Models\Signature::where('backend_signature', Input::get('backend_signature'))->first();

            $result = '';
            if(!$signat){
                // tidak ada signature ganda
                $result = 1;
            }else{
                $result = 0;
            }
        }
        
    
        return $result;
    }

    public static function dekrip_signature() 
    {
        $tes = self::setting('signature_testing');
        if($tes==1)
        {
            $result = [
                    'status' => 1,
                    'message' => 'Signature sukses'
                ];
        }else{
            $validasi    = MasterSetting::whereIn('setting_name', ['iv','key'])->get();

            $iv     = $validasi[0]->setting_value;
            $key     = $validasi[1]->setting_value;
            $OPENSSL_CIPHER_NAME = "aes-128-cbc";
            $CIPHER_KEY_LEN = 16;

            if (strlen($key) < $CIPHER_KEY_LEN) 
            {
                $key = str_pad("$key", $CIPHER_KEY_LEN, "0"); //0 pad to len 16
            } else if (strlen($key) > $CIPHER_KEY_LEN) {
                $key = substr($str, 0, $CIPHER_KEY_LEN); //truncate to 16 bytes
            }

            $decryptedData = openssl_decrypt(base64_decode(Input::get('backend_signature')), $OPENSSL_CIPHER_NAME, $key, OPENSSL_RAW_DATA, $iv);
            
            if (strpos( $decryptedData, '&&' ) !== false)      
            {
                $parts = explode('&&', $decryptedData);
                $device_id = $parts[0];
                $waktu = $parts[1];

                if($device_id == '' or $waktu == '') 
                {
                    $result = [
                        'status' => 0,
                        'message' => 'Signature error 1'
                    ];
                }else{
                    
                    // save signature
                    self::simpan_signature($device_id,$waktu,Input::get('backend_signature'));

                    $result = [
                        'status' => 1,
                        'message' => 'Signature sukses'
                    ];
                    

                    
                }
            }else{
                $result = [
                        'status' => 0,
                        'message' => 'Signature error 2'
                    ];
            }
        }
        

        return $result;

    }

    public static function cekAksesRoute()
    {
        // Cek Route
        $user = \App\Models\User::where('email', Input::get('email'))->orderBy('id', 'desc')->first();
        $acl = \App\Models\Accesscontrol::where('id', $user->accesscontrol_id)->where('route_access_list', 'LIKE', '%'.Input::get('route').'%')->count(); 
        // Log::Info('user '.json_encode($user));
        // Log::Info('acl '.$acl);
        if($acl > 0)
        {
            // Log::Info('Berhak akses route '.json_encode($acl));

            $result = [
                'status' => 1,
                'message' => 'Sukses'
            ];
        }else{
            Log::Info(Input::get('email').' tidak berhak akses ');
            $result = [
                'status' => 0,
                'message' => 'Tidak berhak akses'
            ];
        }

        return $result;

    }

    public static function cek_user($user, $id) 
    {
        $tes = \App\Models\User::where('email', $user)
        ->where('id', '!=', $id)
        ->first();

        
        if($tes)
        {
            $result = 0;    
        }else{
            $result = 1;
        }        
    
        return $result;
    }

    public static function simpan_signature($device_id,$waktu,$signature) 
    {
        $result = new \App\Models\Signature;
        $result->device_id  = $device_id;   
        $result->waktu      = $waktu;
        $result->backend_signature     = $signature;
        $result->user     = Input::get('email');
        $result->save();
        return $result;
    }

    public static function cekKursiAvailable() 
    {
        $bus = \App\Models\Master_bus::find(Input::get('bus_id'));
        $lolos=1;
        $terjual = explode(",", $bus->kursi_terjual);
        $booking = explode(',', $bus->kursi_booking);
        $nomor_kursi = explode(',', Input::get('nomor_kursi'));

        // CEK DI KURSI TERJUAL
        if(count($terjual) > 0)
        {
            $cek=array_intersect($terjual,$nomor_kursi);
            if(count($cek) > 0)
            {
                $lolos=0;
            }else{
                // sukses
                $lolos=1;
            }
        }else{
            $lolos=1;
        }

        if($lolos==0)
        {
            $result = [
                'status' => 0,
                'message' => 'Nomor kursi tidak tersedia, silahkan pilih nomor kursi lain'
            ];
        }else{
            // CEK DI KURSI BOOKING
            $booking = explode(',', $bus->kursi_booking);
            $nomor_kursi = explode(',', Input::get('nomor_kursi'));

            if(count($booking) > 0)
            {
                $cek=array_intersect($booking,$nomor_kursi);
                if(count($cek) > 0)
                {
                    $result = [
                        'status' => 0,
                        'message' => 'Nomor kursi tidak tersedia, silahkan pilih nomor kursi lain'
                    ];
                }else{
                    // sukses
                    if($bus->kursi_booking=='')
                    {
                        $bus->kursi_booking = Input::get('nomor_kursi');
                    }else{
                        $bus->kursi_booking = $bus->kursi_booking.','.Input::get('nomor_kursi');
                    }
                    $bus->update();
                    Log::Info(1);
                    $result = [
                        'status' => 1,
                        'message' => 'Sukses'
                    ];
                }
            }else{
                Log::Info(2);
                $bus->kursi_booking = Input::get('nomor_kursi');
                $bus->update();

                $result = [
                    'status' => 1,
                    'message' => 'Sukses'
                ];
            }

//             if($bus->kursi_booking == '')
//             {
                
//             }elseif(strpos($bus->kursi_booking, ','))
//             {
//                 if(strpos(Input::get('nomor_kursi'), ',') === true)
//                 {
//                     $nk = explode(",", Input::get('nomor_kursi'));
//                     $cek=array_intersect($booking,$nk);
//                     if(count($cek) > 0)
//                     {
//                         $result = [
//                             'status' => 0,
//                             'message' => 'Silahkan pilih nomor kursi lain'
//                         ];
//                     }else{
//                         // sukses
//                         $bus->kursi_booking = $bus->kursi_booking.','.Input::get('nomor_kursi');
//                         $bus->update();
// Log::Info(2);
//                         $result = [
//                             'status' => 1,
//                             'message' => 'Sukses'
//                         ];
//                     }

//                 }else{
//                     if(in_array(Input::get('nomor_kursi'), $booking))
//                     {
//                         $result = [
//                             'status' => 0,
//                             'message' => 'Silahkan pilih nomor kursi lain'
//                         ];
//                     }else{
//                         // sukses
//                         $bus->kursi_booking = $bus->kursi_booking.','.Input::get('nomor_kursi');
//                         $bus->update();
// Log::Info(3);
//                         $result = [
//                             'status' => 1,
//                             'message' => 'Sukses'
//                         ];
//                     }                                
//                 } 
//             }elseif(strlen($bus->kursi_booking) > 0 && !strpos($bus->kursi_booking, ','))
//             {
//                 if(strpos(Input::get('nomor_kursi'), ',') === true)
//                 {
//                     $nk = explode(",", Input::get('nomor_kursi'));
//                     if(in_array($bus->kursi_booking, $nk))
//                     {
//                         $result = [
//                             'status' => 0,
//                             'message' => 'Silahkan pilih nomor kursi lain'
//                         ];
//                     }else{
//                         // sukses
//                         $bus->kursi_booking = $bus->kursi_booking.','.Input::get('nomor_kursi');
//                         $bus->update();
// Log::Info(4);
//                         $result = [
//                             'status' => 1,
//                             'message' => 'Sukses'
//                         ];
//                     } 

//                 }else{
//                     if(Input::get('nomor_kursi') == $booking)
//                     {
//                         $result = [
//                             'status' => 0,
//                             'message' => 'Silahkan pilih nomor kursi lain'
//                         ];
//                     }else{
//                         // sukses
//                         $bus->kursi_booking = $bus->kursi_booking.','.Input::get('nomor_kursi');
//                         $bus->update();
// Log::Info(5);
//                         $result = [
//                             'status' => 1,
//                             'message' => 'Sukses'
//                         ];
//                     }                                
//                 } 
//             }

        }

        if($result['status'] == 1)
        {
            // UPDATE KURSI SISA
            $all = explode(',', $bus->kursi_tersedia);
            $gabung = array_merge(explode(',', $bus->kursi_booking),explode(',', $bus->kursi_terjual));
            $sisa = array_diff($all, $gabung);
            $sisa = implode(',', $sisa);
            $bus->kursi_sisa = $sisa;
            $bus->update();
        }

        return $result;
        


    }

}