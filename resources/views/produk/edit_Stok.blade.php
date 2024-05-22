@extends('layouts.master')

@section('title')
    Daftar Produk
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Daftar Produk</li>
@endsection

@section('content')
<div class="row">

    <div class="col-lg-4">
        @if(session()->has('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
                Data berhasil disimpan.
          </div>
        @endif
        {{-- notifikasi form validasi --}}
		@if ($errors->has('file'))
		<span class="invalid-feedback" role="alert">
			<strong>{{ $errors->first('file') }}</strong>
		</span>
		@endif
 
		{{-- notifikasi sukses --}}
		@if ($sukses = Session::get('sukses'))
		<div class="alert alert-success alert-block">
			<button type="button" class="close" data-dismiss="alert">×</button> 
			<strong>{{ $sukses }}</strong>
		</div>
		@endif
    </div>
    <div class="col-lg-12">
        
        <div class="box">

        <form action="{{ route('produk.edit_stok') }}" method="post" class="form-horizontal" enctype="multipart/form-data">
            @csrf
            @method('post')

            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">

                    <div class="form-group row">
                        <label for="nama_produk" class="col-lg-2 col-lg-offset-1 control-label">Nama Barang</label>
                        <div class="col-lg-6">
                            <input type="text" name="nama_produk" id="nama_produk" value="{{ $barang->nama_produk }}" class="form-control" required readonly>
                            <input type="hidden" name="id_produk" id="" value="{{ $barang->id_produk }}" class="form-control" required readonly>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="nama_produk" class="col-lg-2 col-lg-offset-1 control-label">Stok Barang</label>
                        <div class="col-lg-6">
                            <input type="text" name="stok" id="" class="form-control" required autofocus>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="id_kategori" class="col-lg-2 col-lg-offset-1 control-label">Cabang</label>
                        <div class="col-lg-6">
                            <select name="cabang" id="id_kategori" class="form-control" required>
                                <option value="">Pilih Cabang</option>
                                @foreach ($cabang as  $item)
                                <option value="{{ $item->id_cabang }}">{{ $item->nama_cabang }}</option>
                                @endforeach
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    
                   
                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-flat btn-primary"><i class="fa fa-save"></i> Simpan</button>
                    <button type="button" class="btn btn-sm btn-flat btn-warning" data-dismiss="modal"><i class="fa fa-arrow-circle-left"></i> Batal</button>
                </div>
            </div>
        </form>

        </div>
    </div>
</div>

@endsection

