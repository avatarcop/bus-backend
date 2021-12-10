<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    public $table    ="transaksi";

    public function customer() {
        return $this->belongsTo('\App\Models\Customer', 'cust_id', 'id');
    }

    public function masterpo() {
        return $this->belongsTo('\App\Models\Master_po', 'po_id', 'id');
    }

    public function masterbus() {
        return $this->belongsTo('\App\Models\Master_bus', 'bus_id', 'id');
    }

}
