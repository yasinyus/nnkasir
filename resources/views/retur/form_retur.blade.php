@extends('layouts.master')

@section('title')
    Form Retur
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Form Retur</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3 class="text-center">Form Retur Barang</h3>
        <div class="box">
            <form action="{{ route('retur.store') }}" method="post" class="form-horizontal">
                @csrf
                @method('post')
                <input type="hidden" name="id_penjualan" value="{{ $_GET['id'] }}">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"></h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label for="nama_cabang" class="col-lg-2 col-lg-offset-1 control-label">Jumlah Barang retur</label>
                            <div class="col-lg-6">
                                <input type="number" name="jumlah_retur" id="" class="form-control" required autofocus>
                                <span class="help-block with-errors"></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label for="nama_cabang" class="col-lg-2 col-lg-offset-1 control-label">Jenis Retur</label>
                            <div class="col-lg-6">
                                <select name="jenis_retur" id="" class="form-control" required>
                                    <option value="">Pilih Jenis Retur</option>
                                    <option value="kembali">Pengembalian Barang</option>
                                    <option value="tukar">Penukaran Barang</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label for="nama_cabang" class="col-lg-2 col-lg-offset-1 control-label">Alasan Retur</label>
                            <div class="col-lg-6">
                                <textarea class="form-control" name="alasan_retur" id="" cols="30" rows="5" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label for="nama_cabang" class="col-lg-2 col-lg-offset-1 control-label">.</label>
                            <div class="col-lg-6">
                                <button class="btn btn-sm btn-flat btn-primary"><i class="fa fa-save"></i> SIMPAN</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

