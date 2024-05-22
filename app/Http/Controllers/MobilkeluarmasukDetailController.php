<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Mobil;
use App\Models\Mobilkeluarmasuk;
use App\Models\Mobilkeluarmasukdetail;
use App\Models\Produk;
use App\Models\Setting;
use App\Models\Supplier;
use Illuminate\Http\Request;
use MobilkeluarmasukdetailTable;

class MobilkeluarmasukDetailController extends Controller
{
    public function index()
    {
        $id_keluarmasuk = session('id_keluarmasuk');
        $mobil = Mobil::find(session('id_mobil'));
        $jenis = Kategori::where('jenis', 'Jasa')->get();
        $diskon = Setting::first()->diskon ?? 0;
        $total = Mobilkeluarmasukdetail::where('id_keluarmasuk', session('id_keluarmasuk'))->sum('biaya');
        $status = Mobilkeluarmasuk::find(session('id_keluarmasuk'));


        return view('keluarmasuk_detail.index', compact('id_keluarmasuk', 'mobil', 'jenis','diskon', 'total', 'status'));
    }

    public function edit_detail($id, $id_mobil)
    {
        $id_keluarmasuk = $id;
        $mobil = Mobil::find($id_mobil);
        $jenis = Kategori::where('jenis', 'Jasa')->get();
        $diskon = Setting::first()->diskon ?? 0;
        $total = Mobilkeluarmasukdetail::where('id_keluarmasuk', $id)->sum('biaya');
        $status = Mobilkeluarmasuk::find($id);


        return view('keluarmasuk_detail.index', compact('id_keluarmasuk', 'mobil', 'jenis','diskon', 'total', 'status'));
    }

    public function data($id)
    {
        $detail = Mobilkeluarmasukdetail::where('id_keluarmasuk', $id)->get();
        $data = array();
        $total = 0;
        $total_item = 0;

        foreach ($detail as $item) {
            $row = array();
            $row['jenis_detail'] = $item->jenis_detail;
            $row['uraian']      = $item->uraian;
            $row['biaya']  = 'Rp. '. format_uang($item->biaya);
            $row['aksi']        = '<div class="btn-group">
                                    <button onclick="deleteData(`'. route('keluarmasuk_detail.destroy', $item->id_keluarmasukdetail) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                                </div>';
            $data[] = $row;

            $total += $item->harga_beli * $item->jumlah;
            $total_item += $item->jumlah;
        }
        $data[] = [
            'jenis_detail' => '',
            'uraian'  => '',
            'biaya'      => '',
            'aksi'        => '',
        ];

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->rawColumns(['jenis_detail', 'uraian', 'biaya', 'aksi'])
            ->make(true);
    }

    public function store(Request $request)
    {

        $detail = new Mobilkeluarmasukdetail();
        $detail->id_keluarmasuk = $request->id_keluarmasuk;
        $detail->id_mobil = $request->id_mobil;
        $detail->tanggal_trans = date("Y/m/d");
        $detail->jenis_detail = $request->jenis_detail;
        $detail->uraian = $request->uraian;
        $detail->biaya = $request->biaya;
        $detail->save();

        return redirect()->route('keluarmasuk_detail.index');
    }

    public function update(Request $request, $id)
    {
        $detail = PembelianDetail::find($id);
        $detail->jumlah = $request->jumlah;
        $detail->subtotal = $detail->harga_beli * $request->jumlah;
        $detail->update();
    }

    public function destroy($id)
    {
        $detail = Mobilkeluarmasukdetail::find($id);
        $detail->delete();

        return response(null, 204);
    }

    public function loadForm($diskon = 0, $total = 0,)
    {
        $bayar = $total - ($diskon / 100 * $total);
        $data  = [
            'totalrp' => format_uang($total),
            'bayar' => $bayar,
            'bayarrp' => format_uang($bayar),
            'terbilang' => ucwords(terbilang($bayar). ' Rupiah')
        ];

        return response()->json($data);
    }
}
