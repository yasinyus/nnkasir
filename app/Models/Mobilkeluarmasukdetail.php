<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mobilkeluarmasukdetail extends Model
{
    use HasFactory;

    protected $table = 'mobil_keluarmasukdetail';
    protected $primaryKey = 'id_keluarmasukdetail';
    protected $guarded = [];

    // public function mobil()
    // {
    //     return $this->belongsTo(Mobil::class, 'id_mobil', 'id_mobil');
    // }
}
