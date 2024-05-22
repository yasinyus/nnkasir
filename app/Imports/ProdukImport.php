<?php

namespace App\Imports;

use App\Models\Produk;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ProdukImport implements ToModel, WithStartRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Produk([
            'kode_produk' => $row[0],
            'nama_produk' => $row[1], 
            'stok' => $row[2], 
            'harga_beli' => $row[3], 
            'harga_jual' => $row[4], 
            'harga_jual2' => $row[5], 
            'harga_jual3' => $row[6],
        ]);
    }

    public function startRow(): int
    {
        return 2;
    }
}
