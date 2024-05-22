<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stokbarang extends Model
{
    use HasFactory;

    protected $table = 'stok_barang';
    protected $primaryKey = 'id_stok';
    protected $guarded = [];
}
