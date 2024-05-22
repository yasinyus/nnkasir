<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mobilkeluarmasuk extends Model
{
    use HasFactory;

    protected $table = 'mobil_keluarmasuk';
    protected $primaryKey = 'id_keluarmasuk';
    protected $guarded = [];

    public function mobil()
    {
        return $this->belongsTo(Mobil::class, 'id_mobil', 'id_mobil');
    }
}
