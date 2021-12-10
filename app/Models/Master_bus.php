<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Master_bus extends Model
{
    public $table    ="master_bus";

    public function masterpo() {
        return $this->belongsTo('\App\Models\Master_po', 'po_id', 'id');
    }

    public function tipekursi() {
        return $this->belongsTo('\App\Models\Master_tipekursi', 'tipekursi_id', 'id');
    }
}
