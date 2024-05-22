<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Mobil;
use App\Models\Mobilkeluarmasuk;
use App\Models\Mobilkeluarmasukdetail;
use Illuminate\Http\Request;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\Produk;
use App\Models\Setting;
use App\Models\Supplier;
use PDF;

class MobilkeluarmasukController extends Controller
{
    public function index(Request $request)
    {
        
        if(auth()->user()->user_cabang == 0) {
            $cabang = $request->cabang;
        } else {
            $cabang = auth()->user()->user_cabang;
        }

        $keluarmasuk = Mobilkeluarmasuk::query()
        ->leftJoin('mobil', 'mobil.id_mobil', '=', 'mobil_keluarmasuk.id_mobil')
        ->leftJoin('cabang', 'cabang.id_cabang', '=', 'mobil_keluarmasuk.id_cabang')
        ;

        $pendapatan = Mobilkeluarmasuk::query();

        if(isset($_GET['start_date'])) {} else { $_GET['start_date'] = NULL;}
        if(isset($_GET['end_date'])) {} else { $_GET['end_date'] = NULL;}
        if(isset($_GET['status'])) {} else { $_GET['status'] = NULL;}
        if(isset($_GET['waktu'])) {} else { $_GET['waktu'] = NULL;}
        if(isset($_GET['cabang'])) {} else { $_GET['cabang'] = NULL;}
        
        if( isset($request->start_date) && ($request->start_date != null) ) {
            $keluarmasuk->whereBetween('tanggal_service', [$request->get('start_date'), $request->get('end_date')]);
            $pendapatan->whereBetween('tanggal_service', [$request->get('start_date'), $request->get('end_date')]);
        } 
        // else {
        //     $keluarmasuk->where('tanggal_service', date('Y-m-d'));
        //     $pendapatan->where('tanggal_service', date('Y-m-d'));
        // }

        if( isset($request->status) && ($request->status != null) ) {
            $keluarmasuk->where('status_trans', $request->status);
            $pendapatan->where('status_trans', $request->status);
        }

        if( isset($request->waktu) && ($request->waktu != null) ) {
            $keluarmasuk->where('tanggal_service', '>', now()->subDays($request->get('waktu'))->endOfDay());
            $pendapatan->where('tanggal_service', '>', now()->subDays($request->get('waktu'))->endOfDay());
        }

        if(auth()->user()->user_cabang == 0) {
            if( isset($request->cabang) && ($request->cabang != null) ) {
                $keluarmasuk->where('mobil_keluarmasuk.id_cabang', $cabang);
                $pendapatan->where('mobil_keluarmasuk.id_cabang', $cabang);
            }
        } else {
           
                $keluarmasuk->where('mobil_keluarmasuk.id_cabang', $cabang);
                $pendapatan->where('mobil_keluarmasuk.id_cabang', $cabang);
            
        } 
        
        $keluarmasuk_akhir = $keluarmasuk->orderBy('mobil_keluarmasuk.created_at', 'DESC')->get();
        $mobil = Mobil::orderBy('nama_pemilik')->get();
        $pendapatan_total = $pendapatan->sum('total_harga');
        $cabang = Cabang::orderBy('nama_cabang')->get();

        return view('keluarmasuk.index', compact('mobil', 'keluarmasuk_akhir', 'pendapatan_total', 'cabang'));
    }

    public function data()
    {
        
    }

    public function create($id)
    {
        $keluarmasuk = new Mobilkeluarmasuk();
        $keluarmasuk->id_mobil = $id;
        $keluarmasuk->tanggal_service = date('Y/m/d');
        $keluarmasuk->total_harga= 0;
        $keluarmasuk->bayar = 0;
        $keluarmasuk->diterima = 0;
        $keluarmasuk->kurang = 0;
        $keluarmasuk->status_trans = null;
        $keluarmasuk->save();

        session(['id_keluarmasuk' => $keluarmasuk->id_keluarmasuk]);
        session(['id_mobil' => $keluarmasuk->id_mobil]);

        return redirect()->route('keluarmasuk_detail.index');
    }

    public function store(Request $request)
    {
        $kelurmasuk = Mobilkeluarmasuk::findOrFail($request->id_keluarmasuk);
        $kelurmasuk->id_cabang = auth()->user()->user_cabang;
        $kelurmasuk->total_harga = $request->total_harga;
        $kelurmasuk->bayar = $request->bayar;
        $kelurmasuk->status_trans = $request->status_trans;
        $kelurmasuk->update();

        return redirect()->route('keluarmasuk.index');
    }

    public function show($id)
    {
        $detail = PembelianDetail::with('produk')->where('id_pembelian', $id)->get();

        return datatables()
            ->of($detail)
            ->addIndexColumn()
            ->addColumn('kode_produk', function ($detail) {
                return '<span class="label label-success">'. $detail->produk->kode_produk .'</span>';
            })
            ->addColumn('nama_produk', function ($detail) {
                return $detail->produk->nama_produk;
            })
            ->addColumn('harga_beli', function ($detail) {
                return 'Rp. '. format_uang($detail->harga_beli);
            })
            ->addColumn('jumlah', function ($detail) {
                return format_uang($detail->jumlah);
            })
            ->addColumn('subtotal', function ($detail) {
                return 'Rp. '. format_uang($detail->subtotal);
            })
            ->rawColumns(['kode_produk'])
            ->make(true);
    }

    public function destroy($id)
    {
        $keluarmasuk = Mobilkeluarmasuk::find($id);
        $detail    = Mobilkeluarmasukdetail::where('id_keluarmasuk', $id)->get();
        foreach ($detail as $item) {
            $item->delete();
        }

        $keluarmasuk->delete();

        return response(null, 204);
    }

    public function nota_mobil_keluarmasuk($id)
    {
        $setting = Setting::first();
        $keluarmasuk = Mobilkeluarmasuk::find($id);
        $id_mobil = $keluarmasuk->id_mobil;
        $mobil = Mobil::where('id_mobil', $id_mobil)->first();
        $detail = Mobilkeluarmasukdetail::where('id_keluarmasuk', $id)->get();

        $pdf = PDF::loadView('keluarmasuk.nota_besar', compact('setting', 'keluarmasuk', 'detail', 'mobil'));
        $pdf->setPaper(0,0,609,440, 'potrait');
        return $pdf->stream('Mobil_Keluar_Masuk-'. date('Y-m-d-his') .'.pdf');
    }
}
