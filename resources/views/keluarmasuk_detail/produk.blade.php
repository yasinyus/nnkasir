<div class="modal fade" id="modal-produk" tabindex="-1" role="dialog" aria-labelledby="modal-produk">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header flex">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                    <div class="col-6"> <h4 class="modal-title">Tambahkan Uraian</h4></div>
               
                
            </div>
            
            <div class="modal-body">
                <form class="form-produk" action="{{ route("keluarmasuk_detail.store") }}" method="post">
                    @csrf
                    @method('post')
                    <div class="form-group row">
                        <label for="" class="col-lg-2 col-lg-offset-1 control-label">Jenis</label>
                        <div class="col-lg-6">
                            <select class="form-control" name="jenis_detail" id="">
                                @foreach ($jenis as $item )
                                    <option>{{ $item->nama_kategori }}</option>
                                @endforeach
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <input type="hidden" value="{{ $id_keluarmasuk }}" name="id_keluarmasuk">
                    <input type="hidden" value="{{ $mobil->id_mobil }}" name="id_mobil">
                    <div class="form-group row">
                        <label for="" class="col-lg-2 col-lg-offset-1 control-label">Uraian</label>
                        <div class="col-lg-6">
                            <input type="text" name="uraian" id="uraian" class="form-control" required autofocus>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="col-lg-2 col-lg-offset-1 control-label">Biaya</label>
                        <div class="col-lg-6">
                            <input type="number" name="biaya" id="biaya" class="form-control" required autofocus>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="col-lg-2 col-lg-offset-1 control-label"></label>
                        <div class="col-lg-6">
                            <button type="submit" name="simpan" class="btn btn-danger">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>