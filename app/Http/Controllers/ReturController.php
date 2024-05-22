<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Stokbarang;
use Illuminate\Http\Request;

class ReturController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(auth()->user()->user_cabang == 0) {
            $cabang = $request->cabang;
        } else {
            $cabang = auth()->user()->user_cabang;
        }

        if(isset($_GET['start_date'])) {} else { $_GET['start_date'] = NULL;}
        if(isset($_GET['end_date'])) {} else { $_GET['end_date'] = NULL;}
        if(isset($_GET['cabang'])) {} else { $_GET['cabang'] = NULL;}

        $data_query = PenjualanDetail::query()
        ->leftJoin('produk', 'produk.id_produk', '=', 'penjualan_detail.id_produk')
        ->leftJoin('cabang', 'cabang.id_cabang', '=', 'penjualan_detail.id_cabang')
        ->where('is_retur', 1)
        ;

        if( isset($request->start_date) && ($request->start_date != null) ) {
            $data_query->whereBetween('retur_date', [$request->get('start_date'), $request->get('end_date')]);
        }

        if(auth()->user()->user_cabang == 0) {
            if( isset($request->cabang) && ($request->cabang != null) ) {
                $data_query->where('penjualan_detail.id_cabang', $cabang);
            }
        } else {
                $data_query->where('penjualan_detail.id_cabang', $cabang);

        } 

        $data = $data_query->orderBy('retur_date', 'DESC')->get();
        $cabang = Cabang::orderBy('nama_cabang')->get();
        // $data = PenjualanDetail::leftJoin('produk', 'produk.id_produk', '=', 'penjualan_detail.id_produk')
        // ->leftJoin('cabang', 'cabang.id_cabang', '=', 'penjualan_detail.id_cabang')
        // ->where('is_retur', 1)->get();
        return view('retur.index', compact('data', 'cabang'));
    }

    public function data(Request $request)
    {

        $cek = Penjualan::where('no_nota', $request->no_nota)->first();

        $data = PenjualanDetail::leftJoin('produk', 'produk.id_produk', '=', 'penjualan_detail.id_produk')
        ->leftJoin('cabang', 'cabang.id_cabang', '=', 'penjualan_detail.id_cabang')
        ->leftJoin('penjualan', 'penjualan.id_penjualan', '=', 'penjualan_detail.id_penjualan')
        ->where('penjualan_detail.id_penjualan', $cek->id_penjualan)->get();

        return view('retur.data', compact('data'));
        

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('retur.form_retur');
        
       
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = PenjualanDetail::find($request->id_penjualan);
        $stoks = Stokbarang::where('id_cabang', $data->id_cabang)
        ->where('id_produk', $data->id_produk)->first();

        // dd($stoks->jumlah_stok_cabang);

        $harga_peritem = $data->subtotal / $data->jumlah;
        $harga_totalitem = $harga_peritem * $request->jumlah_retur;
        $id_cab = $data->id_cabang;
        $id_prod = $data->id_produk;
        $stok_baru = $stoks->jumlah_stok_cabang + $request->jumlah_retur;
    

        if($request->jenis_retur == 'kembali'){

            Stokbarang::where(function($query) use ($id_cab, $id_prod) {
                $query->where('id_cabang', $id_cab)
                    ->where('id_produk', $id_prod);
            })->update(['jumlah_stok_cabang' => $stok_baru]);
         

            $data->jumlah = $data->jumlah - $request->jumlah_retur;
            $data->is_retur = 1;
            $data->jumlah_retur = $request->jumlah_retur;
            $data->subtotal = $data->subtotal - $harga_totalitem;
            $data->retur_date = date('Y-m-d');
            $data->jenis_retur = $request->jenis_retur;
            $data->alasan_retur = $request->alasan_retur;
        } else {
            $data->is_retur = 1;
            $data->jumlah_retur = $request->jumlah_retur;
            $data->retur_date = date('Y-m-d');
            $data->jenis_retur = $request->jenis_retur;
            $data->alasan_retur = $request->alasan_retur;
        }

        $data->update();

        return redirect()->route('retur.index')->withSuccess(__('Retur Disimpan.'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Stokbarang::find($id);

        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = Stokbarang::find($id);
        $data->nama_cabang = $request->nama_cabang;
        $data->alamat_cabang = $request->alamat_cabang;
        $data->update();

        return response()->json('Data berhasil disimpan', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $kategori = Stokbarang::find($id);
        $kategori->delete();

        return response(null, 204);
    }
}
