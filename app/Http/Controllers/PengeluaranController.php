<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use Illuminate\Http\Request;
use App\Models\Pengeluaran;

class PengeluaranController extends Controller
{
    public function index(Request $request)
    {
        if(auth()->user()->user_cabang == 0) {
            $cabang = $request->cabang;
        } else {
            $cabang = auth()->user()->user_cabang;
        }

        $query_pengeluaran = Pengeluaran::query()
        ->leftJoin('users', 'users.id', '=', 'pengeluaran.id_user')
        ->leftJoin('cabang', 'cabang.id_cabang', '=', 'pengeluaran.id_cabang')
        ;

        $query_harga_pengeluaran = Pengeluaran::query();

        if(isset($_GET['start_date'])) {} else { $_GET['start_date'] = NULL;}
        if(isset($_GET['end_date'])) {} else { $_GET['end_date'] = NULL;}
        if(isset($_GET['cabang'])) {} else { $_GET['cabang'] = NULL;}
        
        if( isset($request->start_date) && ($request->start_date != null) ) {
            $query_pengeluaran->whereBetween('tgl_pengeluaran', [$request->get('start_date'), $request->get('end_date')]);
            $query_harga_pengeluaran->whereBetween('tgl_pengeluaran', [$request->get('start_date'), $request->get('end_date')]);
        }

        if(auth()->user()->user_cabang == 0) {
            if( isset($request->cabang) && ($request->cabang != null) ) {
                $query_pengeluaran->where('pengeluaran.id_cabang', $cabang);
                $query_harga_pengeluaran->where('pengeluaran.id_cabang', $cabang);
            }
        } else {
           
                $query_pengeluaran->where('pengeluaran.id_cabang', $cabang);
                $query_harga_pengeluaran->where('pengeluaran.id_cabang', $cabang);
            
        } 

        $pengeluaran = $query_pengeluaran->orderBy('pengeluaran.created_at', 'DESC')->get();
        $total_pengeluaran = $query_harga_pengeluaran->sum('nominal');

        $cabang = Cabang::orderBy('nama_cabang')->get();
        
        return view('pengeluaran.index', compact('pengeluaran', 'total_pengeluaran', 'cabang'));
    }

    public function data()
    {
       
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

        $pengeluaran = new Pengeluaran();
        $pengeluaran->id_cabang = auth()->user()->user_cabang;
        $pengeluaran->id_user = auth()->id();
        $pengeluaran->tgl_pengeluaran = date('Y-m-d');
        $pengeluaran->deskripsi = $request->deskripsi;
        $pengeluaran->nominal = $request->nominal;
        $pengeluaran->save();
        // $pengeluaran = Pengeluaran::create($request->all());

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
        $pengeluaran = Pengeluaran::find($id);

        return response()->json($pengeluaran);
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
        $pengeluaran = Pengeluaran::find($id)->update($request->all());

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
        $pengeluaran = Pengeluaran::find($id)->delete();

        return response(null, 204);
    }
}
