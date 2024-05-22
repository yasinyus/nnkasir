<?php

namespace App\Exports;

use App\Models\Produk;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProdukExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
        {
           
            return Produk::select('kode_produk','nama_produk','stok','harga_beli','harga_jual','harga_jual2','harga_jual3')->get();
        }
    
        public function headings(): array
        {
            //Put Here Header Name That you want in your excel sheet 
            return [
                'KODE BARANG',
                'NAMA BARANG',
                'STOK',
                'HARGA POKOK',
                'HARGA JUAL 1',
                'HARGA JUAL 2',
                'HARGA JUAL 3',
            ];
        }
}
