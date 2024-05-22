<div class="modal fade" id="modal-supplier" tabindex="-1" role="dialog" aria-labelledby="modal-supplier">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Pilih Pelanggan</h4>
                <div class="col-6 float-right"><a href="/mobil" class="btn btn-primary"><i class="fa fa-plus"></i> Tambah Pelanggan baru</a></div>
               
            </div>
            <div class="modal-body">
                <table class="table table-striped table-bordered table-supplier">
                    <thead>
                        <th width="5%">No</th>
                        <th>Nopol</th>
                        <th>Jenis Mobil</th>
                        <th>Nama Pemilik</th>
                        <th>Telepon</th>
                        <th><i class="fa fa-cog"></i></th>
                    </thead>
                    <tbody>
                        @foreach ($mobil as $key => $item)
                            <tr>
                                <td width="5%">{{ $key+1 }}</td>
                                <td>{{ $item->nopol }}</td>
                                <td>{{ $item->type }}</td>
                                <td>{{ $item->nama_pemilik }}</td>
                                <td>{{ $item->telp }}</td>
                                <td>
                                    <a href="{{ route('keluarmasuk.create', $item->id_mobil) }}" class="btn btn-primary btn-xs btn-flat">
                                        <i class="fa fa-check-circle"></i>
                                        Pilih
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>