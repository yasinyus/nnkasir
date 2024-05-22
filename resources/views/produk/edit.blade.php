@extends('layouts.master')

@section('title')
    Edit Barang
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Edit Barang</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <form action="{{ route('produk.update',$data->id_produk) }}" method="post" class="form-horizontal" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="nama_produk" class="col-lg-2 col-lg-offset-1 control-label">Kode Barang</label>
                        <div class="col-lg-6">
                            <input type="text" name="kode_produk" id="kode_produk" class="form-control" value="{{ $data->kode_produk }}" @error('kode_produk') is-invalid @enderror required autofocus>
                            @error('kode_produk')
                            <span class="invalid-feedback" role="alert">
                                <strong style="color:red">{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="nama_produk" class="col-lg-2 col-lg-offset-1 control-label">Nama Barang</label>
                        <div class="col-lg-6">
                            <input type="text" name="nama_produk" id="nama_produk" class="form-control" value="{{ $data->nama_produk }}" required autofocus>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="id_kategori" class="col-lg-2 col-lg-offset-1 control-label">Kategori</label>
                        <div class="col-lg-6">
                            <select name="id_kategori" id="id_kategori" class="form-control" required>
                                <option value="">Pilih Kategori</option>
                                @foreach ($kategori as $key => $item)
                                <option value="{{ $key }}" @if($key == $data->id_kategori)selected @endif>{{ $item }}</option>
                                @endforeach
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="id_kategori" class="col-lg-2 col-lg-offset-1 control-label">Suplier</label>
                        <div class="col-lg-6">
                            <select name="id_suplier" id="id_suplier" class="form-control" required>
                                <option value="">Pilih Suplier</option>
                                @foreach ($suplier as $key => $item)
                                <option value="{{ $key }}" @if($key == $data->id_suplier)selected @endif>{{ $item }}</option>
                                @endforeach
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="merk" class="col-lg-2 col-lg-offset-1 control-label">Merk</label>
                        <div class="col-lg-6">
                            <input type="text" name="merk" id="merk" class="form-control" value="{{ $data->merk }}">
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="harga_beli" class="col-lg-2 col-lg-offset-1 control-label">Harga Beli</label>
                        <div class="col-lg-6">
                            <input type="number" name="harga_beli" id="harga_beli" class="form-control" value="{{ $data->harga_beli }}" required>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="harga_jual" class="col-lg-2 col-lg-offset-1 control-label">Harga Jual 1</label>
                        <div class="col-lg-6">
                            <input type="number" name="harga_jual" id="harga_jual" class="form-control" value="{{ $data->harga_jual }}" required>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="harga_jual2" class="col-lg-2 col-lg-offset-1 control-label">Harga Jual 2</label>
                        <div class="col-lg-6">
                            <input type="number" name="harga_jual2" id="harga_jual2" class="form-control" value="{{ $data->harga_jual2 }}">
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="harga_jual3" class="col-lg-2 col-lg-offset-1 control-label">Harga Jual 3</label>
                        <div class="col-lg-6">
                            <input type="number" name="harga_jual3" id="harga_jual3" class="form-control" value="{{ $data->harga_jual3 }}">
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="gambar" class="col-lg-2 col-lg-offset-1 control-label">Gambar</label>
                        <div class="col-lg-6">
                            @if($data->gambar != "")
                                <img src="{{ $data->gambar }}" width="200px">
                                <input type="hidden" name="gambar_old" value="{{ $data->gambar }}" class="form-control" >
                            @endif
                            <input type="file" name="gambar" id="gambar" class="form-control" >
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

@includeIf('produk.form')
@endsection

