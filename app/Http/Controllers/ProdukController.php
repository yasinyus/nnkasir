<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use App\Models\Produk;
use App\Exports\ProdukExport;
use App\Imports\ProdukImport;
use App\Models\Cabang;
use App\Models\Stokbarang;
use App\Models\Supplier;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Session as FacadesSession;
use Maatwebsite\Excel\Facades\Excel;

use PDF;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $kategori = Kategori::all()->pluck('nama_kategori', 'id_kategori');
        $kategori = Kategori::where('jenis', 'Barang')->get()->pluck('nama_kategori', 'id_kategori');
        return view('produk.index', compact('kategori'));
    }

    public function data()
    {
        $produk = Produk::leftJoin('kategori', 'kategori.id_kategori', 'produk.id_kategori')
            ->select('produk.*', 'nama_kategori')
            // ->orderBy('kode_produk', 'asc')
            ->get();

        if(auth()->user()->user_cabang == 0) {
            return datatables()
            ->of($produk)
            ->addIndexColumn()
            ->addColumn('select_all', function ($produk) {
                return '
                    <input type="checkbox" name="id_produk[]" value="'. $produk->id_produk .'">
                ';
            })
            ->addColumn('kode_produk', function ($produk) {
                return '<span class="label label-success">'. $produk->kode_produk .'</span>';
            })
            ->addColumn('harga_beli', function ($produk) {
                return format_uang($produk->harga_beli);
            })
            ->addColumn('harga_jual', function ($produk) {
                return format_uang($produk->harga_jual);
            })
            // ->addColumn('stok', function ($produk) {
            //     return format_uang($produk->stok);
            // })
            ->addColumn('aksi', function ($produk) {
                return '
                <div class="btn-group">
                    <a href="'.route('produk.edit', $produk->id_produk).'" type="button" class="btn btn-xs btn-info btn-flat" title="Edit Produk"><i class="fa fa-pencil"></i></a>
                    <button type="button" onclick="deleteData(`'. route('produk.destroy', $produk->id_produk) .'`)" class="btn btn-xs btn-danger btn-flat" title="Hapus Produk"><i class="fa fa-trash"></i></button>
                    <a href="'.route('produk.show', $produk->id_produk).'" type="button" class="btn btn-xs btn-warning btn-flat" title="Edit Stok"><i class="fa fa-eye"></i></a>
                </div>
                ';
            })
            ->rawColumns(['aksi', 'kode_produk', 'select_all'])
            ->make(true);
        } else {
            return datatables()
            ->of($produk)
            ->addIndexColumn()
            ->addColumn('select_all', function ($produk) {
                return '
                    <input type="checkbox" name="id_produk[]" value="'. $produk->id_produk .'">
                ';
            })
            ->addColumn('kode_produk', function ($produk) {
                return '<span class="label label-success">'. $produk->kode_produk .'</span>';
            })
            ->addColumn('harga_beli', function ($produk) {
                return format_uang($produk->harga_beli);
            })
            ->addColumn('harga_jual', function ($produk) {
                return format_uang($produk->harga_jual);
            })
            // ->addColumn('stok', function ($produk) {
            //     return format_uang($produk->stok);
            // })
            ->addColumn('aksi', function ($produk) {
                return '
                <div class="btn-group">
                   
                    
                </div>
                ';
            })
            ->rawColumns(['aksi', 'kode_produk', 'select_all'])
            ->make(true);
        }

       
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $kategori = Kategori::where('jenis', 'Barang')->get()->pluck('nama_kategori', 'id_kategori');
        $suplier = Supplier::get()->pluck('nama', 'id_supplier');
        return view('produk.create', compact('kategori', 'suplier'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'kode_produk'      => 'required|unique:produk',
        ],
        [
            'kode_produk.required'=> 'Harus diisi',
            'kode_produk.unique'=> 'Kode Produk sudah ada', 
        ]);

        $fileName = 'img_' . time() . '.'. $request->gambar->extension();  

        $request->gambar->move(public_path('/img'), $fileName);

        Produk::create([
            'id_kategori'   => $request->id_kategori,
            'id_suplier'    => $request->id_suplier,
            'kode_produk'   => $request->kode_produk,
            'nama_produk'   => $request->nama_produk,
            'merk'          => $request->merk,
            'harga_beli'    => $request->harga_beli,
            'harga_jual'    => $request->harga_jual,
            'harga_jual2'   => $request->harga_jual2,
            'harga_jual3'   => $request->harga_jual3,
            'gambar'        => asset('img/'.$fileName),
        ]);

        return redirect()->route('produk.index')->withSuccess(__('File added successfully.'));
       
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $barang = Produk::where('id_produk', $id)->first();
        $cabang = Cabang::get();

        return view('produk.edit_stok', compact('cabang', 'barang'));
    }

    public function edit_stok(Request $request)
    {
        $cek_stok = Stokbarang::where('id_produk', $request->id_produk)
        ->where('id_cabang', $request->cabang)->first();
        if($cek_stok == NULL) {
            Stokbarang::create([
                'jumlah_stok_cabang'    => $request->stok,
                'id_cabang'             => $request->cabang,
                'id_produk'             => $request->id_produk,
            ]);
    
            return redirect()->route('produk.index')->withSuccess(__('Sukses Ubah Stok.'));
        } else {
            $stok = Stokbarang::where('id_produk', $request->id_produk)
            ->where('id_cabang', $request->cabang)->first();
            $stok->update([
                'jumlah_stok_cabang'    => $request->stok,
            ]);
            return redirect()->route('produk.index')->withSuccess(__('Sukses Ubah Stok.'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Produk::findOrFail($id);
        $suplier = Supplier::get()->pluck('nama', 'id_supplier');
        $kategori = Kategori::where('jenis', 'Barang')->get()->pluck('nama_kategori', 'id_kategori');
        return view('produk.edit', compact('kategori','data','suplier'));
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
        $produk = Produk::find($id);
       
        if($request->gambar != NULL){
            $fileName = 'img_' . time() . '.'. $request->gambar->extension();
            $request->gambar->move(public_path('/img'), $fileName);
            $gambar = asset('img/'.$fileName);
        } else {
            $gambar = $request->gambar_old;
        }
       

        $produk->update([
            'id_kategori'   => $request->id_kategori,
            'id_suplier'    => $request->id_suplier,
            'kode_produk'   => $request->kode_produk,
            'nama_produk'   => $request->nama_produk,
            'merk'          => $request->merk,
            'harga_beli'    => $request->harga_beli,
            'harga_jual'    => $request->harga_jual,
            'harga_jual2'   => $request->harga_jual2,
            'harga_jual3'   => $request->harga_jual3,
            'gambar'        => $gambar,
        ]);

        // $produk->update($request->all());
        
        return redirect()->route('produk.index')->withSuccess(__('Edit data sukses.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $produk = Produk::find($id);
        $produk->delete();

        return response(null, 204);
    }

    public function deleteSelected(Request $request)
    {
        foreach ($request->id_produk as $id) {
            $produk = Produk::find($id);
            $produk->delete();
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

    public function export_produk()
	{
		return Excel::download(new ProdukExport, 'Produk.xlsx');
	}
    public function import_produk(Request $request) 
	{
		// validasi
		$this->validate($request, [
			'file' => 'required|mimes:csv,xls,xlsx'
		]);
 
		// menangkap file excel
		$file = $request->file('file');
 
		// membuat nama file unik
		$nama_file = rand().$file->getClientOriginalName();
 
		// upload ke folder file_siswa di dalam folder public
		$file->move('file_produk',$nama_file);
 
		// import data
		Excel::import(new ProdukImport, public_path('/file_produk/'.$nama_file));
 
		// notifikasi dengan session
		FacadesSession::flash('sukses','Data Siswa Berhasil Diimport!');
 
		// alihkan halaman kembali
		return redirect('/produk');
	}
}
