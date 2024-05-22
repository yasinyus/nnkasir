<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use Illuminate\Http\Request;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\Produk;
use App\Models\Stokbarang;
use App\Models\Supplier;

class PembelianController extends Controller
{
    public function index(Request $request)
    {
        if(auth()->user()->user_cabang == 0) {
            $cabang = $request->cabang;
        } else {
            $cabang = auth()->user()->user_cabang;
        }

        $query_pembelian = Pembelian::query()
        ->join('supplier', 'supplier.id_supplier', '=', 'pembelian.id_supplier')
        ->join('cabang', 'cabang.id_cabang', '=', 'pembelian.id_cabang')
        ->select(
            'supplier.*',
            'pembelian.*',
            'cabang.*',
            'pembelian.id_supplier AS id_sup',
        )
        ;

        $query_harga_pembelian = Pembelian::query();

        if(isset($_GET['start_date'])) {} else { $_GET['start_date'] = NULL;}
        if(isset($_GET['end_date'])) {} else { $_GET['end_date'] = NULL;}
        if(isset($_GET['suplier'])) {} else { $_GET['suplier'] = NULL;}
        if(isset($_GET['cabang'])) {} else { $_GET['cabang'] = NULL;}
        
        if( isset($request->start_date) && ($request->start_date != null) ) {
            $query_pembelian->whereBetween('tgl_pembelian', [$request->get('start_date'), $request->get('end_date')]);
            $query_harga_pembelian->whereBetween('tgl_pembelian', [$request->get('start_date'), $request->get('end_date')]);
        }
        //  else {
        //     $query_pembelian->where('tgl_pembelian', date('Y-m-d'));
        //     $query_harga_pembelian->where('tgl_pembelian', date('Y-m-d'));
        // }

        if( isset($request->suplier) && ($request->suplier != null) ) {
            $query_pembelian->where('pembelian.id_supplier', $request->suplier);
            $query_harga_pembelian->where('id_supplier', $request->suplier);
        }

        if(auth()->user()->user_cabang == 0) {
            if( isset($request->cabang) && ($request->cabang != null) ) {
                $query_pembelian->where('pembelian.id_cabang', $cabang);
                $query_harga_pembelian->where('pembelian.id_cabang', $cabang);
            }
        } else {
           
                $query_pembelian->where('pembelian.id_cabang', $cabang);
                $query_harga_pembelian->where('pembelian.id_cabang', $cabang);
            
        } 
        

        $pembelian = $query_pembelian->orderBy('pembelian.created_at', 'DESC')->get();
        $harga_pembelian = $query_harga_pembelian->sum('total_harga');
        $supplier = Supplier::orderBy('nama')->get();
        $cabang = Cabang::orderBy('nama_cabang')->get();
        return view('pembelian.index', compact('supplier', 'pembelian', 'harga_pembelian', 'cabang'));
    }

    public function data()
    {
        if($_GET['start_date'] != null) {
            $pembelian = Pembelian::orderBy('id_pembelian', 'desc')->get();
        }else{
            $pembelian = Pembelian::orderBy('id_pembelian', 'desc')->get();
        }

        return datatables()
            ->of($pembelian)
            ->addIndexColumn()
            ->addColumn('total_item', function ($pembelian) {
                return format_uang($pembelian->total_item);
            })
            ->addColumn('total_harga', function ($pembelian) {
                return 'Rp. '. format_uang($pembelian->total_harga);
            })
            ->addColumn('bayar', function ($pembelian) {
                return 'Rp. '. format_uang($pembelian->bayar);
            })
            ->addColumn('tanggal', function ($pembelian) {
                return tanggal_indonesia($pembelian->created_at, false);
            })
            ->addColumn('supplier', function ($pembelian) {
                return $pembelian->supplier->nama;
            })
            ->editColumn('diskon', function ($pembelian) {
                return $pembelian->diskon . '%';
            })
            ->addColumn('aksi', function ($pembelian) {
                return '
                <div class="btn-group">
                    <button onclick="showDetail(`'. route('pembelian.show', $pembelian->id_pembelian) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-eye"></i></button>
                    <button onclick="deleteData(`'. route('pembelian.destroy', $pembelian->id_pembelian) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create($id)
    {
        $pembelian = new Pembelian();
        $pembelian->id_supplier = $id;
        $pembelian->total_item  = 0;
        $pembelian->total_harga = 0;
        $pembelian->diskon      = 0;
        $pembelian->bayar       = 0;
        $pembelian->tgl_pembelian       = date('Y-m-d');
        $pembelian->save();

        session(['id_pembelian' => $pembelian->id_pembelian]);
        session(['id_supplier' => $pembelian->id_supplier]);

        return redirect()->route('pembelian_detail.index');
    }

    public function store(Request $request)
    {
        if(auth()->user()->user_cabang == 0) {
            $cabang = $request->cabang;
        } else {
            $cabang = auth()->user()->user_cabang;
        }
        $pembelian = Pembelian::findOrFail($request->id_pembelian);
        $pembelian->id_cabang = $cabang;
        $pembelian->total_item = $request->total_item;
        $pembelian->total_harga = $request->total;
        $pembelian->diskon = $request->diskon;
        $pembelian->bayar = $request->bayar;
        $pembelian->update();

        $detail = PembelianDetail::where('id_pembelian', $pembelian->id_pembelian)->get();
        foreach ($detail as $item) {
            $stok_produk = Stokbarang::where('id_produk', $item->id_produk)
            ->where('id_cabang', auth()->user()->user_cabang)
            ->first();

            if($stok_produk == null) {
                $stok = new Stokbarang();
                $stok->jumlah_stok_cabang = $item->jumlah;
                $stok->id_cabang  = auth()->user()->user_cabang;
                $stok->id_produk = $item->id_produk;
                $stok->save();
            } else {
                $stok_produk->jumlah_stok_cabang += $item->jumlah;
                $stok_produk->update();
            }
            
        }

        return redirect()->route('pembelian.index');
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
        $pembelian = Pembelian::find($id);
        $detail    = PembelianDetail::where('id_pembelian', $pembelian->id_pembelian)->get();
        foreach ($detail as $item) {
            $produk = Produk::find($item->id_produk);
            if ($produk) {
                $produk->stok -= $item->jumlah;
                $produk->update();
            }
            $item->delete();
        }

        $pembelian->delete();

        return response(null, 204);
    }
}
