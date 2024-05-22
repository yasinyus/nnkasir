<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Produk;
use App\Models\Setting;
use App\Models\Stokbarang;
use Illuminate\Http\Request;
use PDF;

class PenjualanController extends Controller
{
    public function index(Request $request)
    { 
        $penjualandel = Penjualan::where('id_cabang', 0)->where('total_item', 0)->where('total_harga', 0);
        $penjualandel->delete();
        if(auth()->user()->user_cabang == 0) {
            $cabang = $request->cabang;
        } else {
            $cabang = auth()->user()->user_cabang;
        }

        $query_penjualan = Penjualan::query()
        ->leftJoin('users', 'users.id', '=', 'penjualan.id_user')
        ->leftJoin('cabang', 'cabang.id_cabang', '=', 'penjualan.id_cabang')
        ->leftJoin('member', 'member.id_member', '=', 'penjualan.id_member')
        ;

        $query_harga_penjualan = Penjualan::query();

        if(isset($_GET['start_date'])) {} else { $_GET['start_date'] = NULL;}
        if(isset($_GET['end_date'])) {} else { $_GET['end_date'] = NULL;}
        if(isset($_GET['cabang'])) {} else { $_GET['cabang'] = NULL;}
        
        if( isset($request->start_date) && ($request->start_date != null) ) {
            $query_penjualan->whereBetween('tgl_penjualan', [$request->get('start_date'), $request->get('end_date')]);
            $query_harga_penjualan->whereBetween('tgl_penjualan', [$request->get('start_date'), $request->get('end_date')]);
        }

        if(auth()->user()->user_cabang == 0) {
            if( isset($request->cabang) && ($request->cabang != null) ) {
                $query_penjualan->where('penjualan.id_cabang', $cabang);
                $query_harga_penjualan->where('penjualan.id_cabang', $cabang);
            }
        } else {
           
                $query_penjualan->where('penjualan.id_cabang', $cabang);
                $query_harga_penjualan->where('penjualan.id_cabang', $cabang);
            
        } 

        $penjualan = $query_penjualan->orderBy('penjualan.created_at', 'DESC')->get();
        $total_pendapatan = $query_harga_penjualan->sum('total_harga');

        $cabang = Cabang::orderBy('nama_cabang')->get();
        
        return view('penjualan.index', compact('penjualan', 'total_pendapatan', 'cabang'));
    }

    public function data()
    {
        // $_GET['start_date'] = NULL;
        $tanggal_hariini = date('Y-m-d');
        $penjualan = Penjualan::with('member')->where('updated_at', 'LIKE', "%$tanggal_hariini%")->orderBy('id_penjualan', 'desc')->get();

        return datatables()
            ->of($penjualan)
            ->addIndexColumn()
            ->addColumn('total_item', function ($penjualan) {
                return format_uang($penjualan->total_item);
            })
            ->addColumn('total_harga', function ($penjualan) {
                return 'Rp. '. format_uang($penjualan->total_harga);
            })
            ->addColumn('bayar', function ($penjualan) {
                return 'Rp. '. format_uang($penjualan->bayar);
            })
            ->addColumn('tanggal', function ($penjualan) {
                return tanggal_indonesia($penjualan->created_at, false);
            })
            ->addColumn('kode_member', function ($penjualan) {
                $member = $penjualan->member->kode_member ?? '';
                return '<span class="label label-success">'. $member .'</spa>';
            })
            ->editColumn('diskon', function ($penjualan) {
                return $penjualan->diskon . '%';
            })
            ->editColumn('kasir', function ($penjualan) {
                return $penjualan->user->name ?? '';
            })
            ->addColumn('aksi', function ($penjualan) {
                return '
                <div class="btn-group">
                    <button onclick="showDetail(`'. route('penjualan.show', $penjualan->id_penjualan) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-eye"></i></button>
                    <button onclick="deleteData(`'. route('penjualan.destroy', $penjualan->id_penjualan) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                    <button onclick="notaBesar(`'. route('transaksi.nota_besar_tabel', $penjualan->id_penjualan) .'`)" class="btn btn-xs btn-success btn-flat"><i class="fa fa-print"></i> Besar</button>
                    <button onclick="notaKecil(`'. route('transaksi.nota_kecil_tabel', $penjualan->id_penjualan) .'`)" class="btn btn-xs btn-warning btn-flat"><i class="fa fa-print"></i> Kecil</button>
                </div>
                ';
            })
            ->rawColumns(['aksi', 'kode_member'])
            ->make(true);
    }

    public function create()
    {
        $penjualan_del = Penjualan::where('total_harga', 0);
        $penjualan_del->delete();
        
        $penjualan = new Penjualan();
        $penjualan->id_member = null;
        $penjualan->no_nota = null;
        $penjualan->id_cabang = 0;
        $penjualan->total_item = 0;
        $penjualan->total_harga = 0;
        $penjualan->diskon = 0;
        $penjualan->bayar = 0;
        $penjualan->diterima = 0;
        $penjualan->id_user = auth()->id();
        $penjualan->save();

        session(['id_penjualan' => $penjualan->id_penjualan]);
        return redirect()->route('transaksi.index');
    }

    public function store(Request $request)
    {
        $AWAL = 'TRX';
        // karna array dimulai dari 0 maka kita tambah di awal data kosong
        // bisa juga mulai dari "1"=>"I"
        $bulanRomawi = array("", "I","II","III", "IV", "V","VI","VII","VIII","IX","X", "XI","XII");
        $noUrutAkhir = Penjualan::max('id_penjualan');
        $no = 1;
        if($noUrutAkhir) {
            $no_nota = $AWAL.'/'.sprintf("%03s", abs($noUrutAkhir + 1)) .'/' . $bulanRomawi[date('n')] .'/' . date('Y');
            // echo "No urut surat di database : " . $noUrutAkhir;
            // echo "<br>";
            // echo "Pake Format : " . sprintf("%03s", abs($noUrutAkhir + 1)). '/' . $AWAL .'/' . $bulanRomawi[date('n')] .'/' . date('Y');
        }
        else {
            $no_nota = $AWAL .'/'.sprintf("%03s", $no).'/'.$bulanRomawi[date('n')].'/'.date('Y');
            // echo "No urut surat di database : 0" ;
            // echo "<br>";
            // echo "Pake Format : " . sprintf("%03s", $no). '/' . $AWAL .'/' . $bulanRomawi[date('n')] .'/' . date('Y');
        }

        $penjualan = Penjualan::findOrFail($request->id_penjualan);
        $penjualan->id_member = $request->id_member;
        $penjualan->no_nota = $no_nota;
        $penjualan->id_cabang = auth()->user()->user_cabang;
        $penjualan->total_item = $request->total_item;
        $penjualan->total_harga = $request->total;
        $penjualan->diskon = $request->diskon;
        $penjualan->bayar = $request->bayar;
        $penjualan->diterima = $request->diterima;
        $penjualan->tgl_penjualan = date('Y-m-d');
        $penjualan->update();

        $detail = PenjualanDetail::where('id_penjualan', $penjualan->id_penjualan)->get();
        foreach ($detail as $item) {
            // $item->diskon = $request->diskon;
            // $item->update();

            $stok_produk = Stokbarang::where('id_produk', $item->id_produk)
            ->where('id_cabang', auth()->user()->user_cabang)
            ->first();
            $stok_produk->jumlah_stok_cabang -= $item->jumlah;
            $stok_produk->update();
        }

        return redirect()->route('transaksi.selesai');
    }

    public function show($id)
    {
        $detail = PenjualanDetail::with('produk')->where('id_penjualan', $id)->get();

        return datatables()
            ->of($detail)
            ->addIndexColumn()
            ->addColumn('kode_produk', function ($detail) {
                return '<span class="label label-success">'. $detail->produk->kode_produk .'</span>';
            })
            ->addColumn('nama_produk', function ($detail) {
                return $detail->produk->nama_produk;
            })
            ->addColumn('harga_jual', function ($detail) {
                return 'Rp. '. format_uang($detail->harga_jual);
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
        $penjualan = Penjualan::find($id);
        $detail    = PenjualanDetail::where('id_penjualan', $penjualan->id_penjualan)->get();
        foreach ($detail as $item) {
            $produk = Produk::find($item->id_produk);
            if ($produk) {
                $produk->stok += $item->jumlah;
                $produk->update();
            }

            $item->delete();
        }

        $penjualan->delete();

        return response(null, 204);
    }

    public function selesai()
    {
        $setting = Setting::first();

        return view('penjualan.selesai', compact('setting'));
    }

    public function notaKecil()
    {
        $setting = Setting::first();
        $penjualan = Penjualan::find(session('id_penjualan'));
        if (! $penjualan) {
            abort(404);
        }
        $detail = PenjualanDetail::with('produk')
            ->where('id_penjualan', session('id_penjualan'))
            ->get();
        
        return view('penjualan.nota_kecil', compact('setting', 'penjualan', 'detail'));
    }
    public function notaKecil_tabel($id)
    {
        $setting = Setting::first();
        $penjualan = Penjualan::find($id);
        if (! $penjualan) {
            abort(404);
        }
        $detail = PenjualanDetail::with('produk')
            ->where('id_penjualan', $id)
            ->get();
        
        return view('penjualan.nota_kecil', compact('setting', 'penjualan', 'detail'));
    }

    public function notaBesar()
    {
        $setting = Setting::first();
        $penjualan = Penjualan::find(session('id_penjualan'));
        if (! $penjualan) {
            abort(404);
        }
        $detail = PenjualanDetail::with('produk')
            ->where('id_penjualan', session('id_penjualan'))
            ->get();

        $pdf = PDF::loadView('penjualan.nota_besar', compact('setting', 'penjualan', 'detail'));
        $pdf->setPaper(0,0,609,440, 'potrait');
        return $pdf->stream('Transaksi-'. date('Y-m-d-his') .'.pdf');
    }

    public function notaBesar_tabel($id)
    {
        $setting = Setting::first();
        $penjualan = Penjualan::find($id);
        if (! $penjualan) {
            abort(404);
        }
        $detail = PenjualanDetail::with('produk')
            ->where('id_penjualan', $id)
            ->get();

        $pdf = PDF::loadView('penjualan.nota_besar', compact('setting', 'penjualan', 'detail'));
        $pdf->setPaper(0,0,609,440, 'potrait');
        return $pdf->stream('Transaksi-'. date('Y-m-d-his') .'.pdf');
    }
}
