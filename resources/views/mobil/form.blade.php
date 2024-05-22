<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form">
    <div class="modal-dialog modal-lg" role="document">
        <form action="" method="post" class="form-horizontal">
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
                        <label for="tanggal" class="col-lg-2 col-lg-offset-1 control-label">Tanggal</label>
                        <div class="col-lg-6">
                            <input type="date" name="tanggal" id="tanggal" class="form-control" required autofocus>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="nopol" class="col-lg-2 col-lg-offset-1 control-label">NOPOL</label>
                        <div class="col-lg-6">
                            <input type="text" name="nopol" id="nopol" class="form-control" required autofocus>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="type" class="col-lg-2 col-lg-offset-1 control-label">Jenis Mobil</label>
                        <div class="col-lg-6">
                            <input type="text" name="type" id="type" class="form-control" required autofocus>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="nama_pemilik" class="col-lg-2 col-lg-offset-1 control-label">Nama Pemilik</label>
                        <div class="col-lg-6">
                            <input type="text" name="nama_pemilik" id="nama_pemilik" class="form-control">
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                   
                    <div class="form-group row">
                        <label for="telp" class="col-lg-2 col-lg-offset-1 control-label">Telp</label>
                        <div class="col-lg-6">
                            <input type="text" name="telp" id="telp" class="form-control" >
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="keluhan" class="col-lg-2 col-lg-offset-1 control-label">Keluhan</label>
                        <div class="col-lg-6">
                            <input type="text" name="keluhan" id="keluhan" class="form-control" >
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