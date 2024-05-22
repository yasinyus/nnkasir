<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Member;
use App\Models\Mobil;
use App\Models\Mobilkeluarmasuk;
use App\Models\Pembelian;
use App\Models\Pengeluaran;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\Supplier;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $kategori = Kategori::count();
        $produk = Produk::count();
        $supplier = Supplier::count();
        $member = Member::count();
        $mobil = Mobil::count();

        $tanggal_awal = date('Y-m-01');
        $tanggal_akhir = date('Y-m-d');

        

        $total_transaksi_harian = Penjualan::where('created_at', 'LIKE', "%$tanggal_akhir%")->count();
        $total_penjualan_harian = Penjualan::where('created_at', 'LIKE', "%$tanggal_akhir%")->sum('bayar');
        
        $total_keluarmasuk_harian = Mobilkeluarmasuk::where('tanggal_service', 'LIKE', "%$tanggal_akhir%")->count();
        $total_jasa_harian = Mobilkeluarmasuk::where('tanggal_service', 'LIKE', "%$tanggal_akhir%")->sum('total_harga');
        
        $total_pembelian_harian = Pembelian::where('tgl_pembelian', date('Y-m-d'))->sum('total_harga');
        $total_trans_pembelian_harian = Pembelian::where('tgl_pembelian', date('Y-m-d'))->count();
        
        $total_pengeluaran_harian = Pengeluaran::where('tgl_pengeluaran', date('Y-m-d'))->sum('nominal');
        $total_trans_pengeluaran_harian = Pengeluaran::where('tgl_pengeluaran', date('Y-m-d'))->count();
        // $total_pengeluaran = Pengeluaran::where('created_at', 'LIKE', "%$tanggal_awal%")->sum('nominal');


        $data_tanggal = array();
        $data_pendapatan = array();

        while (strtotime($tanggal_awal) <= strtotime($tanggal_akhir)) {
            $data_tanggal[] = (int) substr($tanggal_awal, 8, 2);

            $total_penjualan = Penjualan::where('created_at', 'LIKE', "%$tanggal_awal%")->sum('bayar');
            $total_pembelian = Pembelian::where('created_at', 'LIKE', "%$tanggal_awal%")->sum('bayar');
            $total_pengeluaran = Pengeluaran::where('created_at', 'LIKE', "%$tanggal_awal%")->sum('nominal');

            $pendapatan = $total_penjualan - $total_pembelian - $total_pengeluaran;
            $data_pendapatan[] += $pendapatan;

            $tanggal_awal = date('Y-m-d', strtotime("+1 day", strtotime($tanggal_awal)));
        }

        $tanggal_awal = date('Y-m-01');

        if (auth()->user()->level == 1) {
            return view('admin.dashboard', compact(
                'kategori', 
                'produk', 
                'supplier', 
                'member', 
                'mobil', 
                'tanggal_awal', 
                'tanggal_akhir', 
                'data_tanggal', 
                'total_transaksi_harian', 
                'total_penjualan_harian', 
                'total_keluarmasuk_harian', 
                'total_jasa_harian', 
                'total_pembelian_harian', 
                'total_trans_pembelian_harian', 
                'total_pengeluaran_harian', 
                'total_trans_pengeluaran_harian', 
                'data_pendapatan'));
        } else {
            return view('kasir.dashboard');
        }
    }
}
