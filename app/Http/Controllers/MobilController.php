<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Mobil;
use Illuminate\Http\Request;
use App\Models\Produk;
use PDF;

class MobilController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $kategori = Kategori::all()->pluck('nama_kategori', 'id_kategori');

        return view('mobil.index', compact('kategori'));
    }

    public function data()
    {
        $produk = Produk::leftJoin('kategori', 'kategori.id_kategori', 'produk.id_kategori')
            ->select('produk.*', 'nama_kategori')
            // ->orderBy('kode_produk', 'asc')
            ->get();

        $mobil = Mobil::get();

        return datatables()
            ->of($mobil)
            ->addIndexColumn()
            ->addColumn('select_all', function ($mobil) {
                return '
                    <input type="checkbox" name="id_mobil[]" value="'. $mobil->id_mobil .'">
                ';
            })
            // ->addColumn('harga_beli', function ($mobil) {
            //     return format_uang($mobil->harga_beli);
            // })
            // ->addColumn('harga_jual', function ($mobil) {
            //     return format_uang($mobil->harga_jual);
            // })
            // ->addColumn('stok', function ($mobil) {
            //     return format_uang($mobil->stok);
            // })
            ->addColumn('aksi', function ($mobil) {
                return '
                <div class="btn-group">
                    <button type="button" onclick="editForm(`'. route('mobil.update', $mobil->id_mobil) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                    <button type="button" onclick="deleteData(`'. route('mobil.destroy', $mobil->id_mobil) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['aksi', 'id_mobil', 'select_all'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $mobil = Mobil::latest()->first() ?? new Mobil();

        $mobil = Mobil::create($request->all());

        return response()->json('Data berhasil disimpan', 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $mobil = Mobil::find($id);

        return response()->json($mobil);
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
        $mobil = Mobil::find($id);
        $mobil->update($request->all());

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
        $mobil = Mobil::find($id);
        $mobil->delete();

        return response(null, 204);
    }

    public function deleteSelected(Request $request)
    {
        foreach ($request->id_mobil as $id) {
            $mobil = Mobil::find($id);
            $mobil->delete();
        }

        return response(null, 204);
    }

    public function cetakBarcode(Request $request)
    {
        $dataproduk = array();
        foreach ($request->id_produk as $id) {
            $produk = Produk::find($id);
            $dataproduk[] = $produk;
        }

        $no  = 1;
        $pdf = PDF::loadView('produk.barcode', compact('dataproduk', 'no'));
        $pdf->setPaper('a4', 'potrait');
        return $pdf->stream('produk.pdf');
    }
}
