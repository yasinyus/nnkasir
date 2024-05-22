@extends('layouts.master')

@section('title')
    Daftar Retur
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Daftar Retur</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3 class="text-center">Data Retur Barang</h3>
        <div class="box">
            {{-- <div class="box-header with-border">
                <button onclick="addForm('{{ route('retur.data') }}')" class="btn btn-success btn-xl btn-flat"><i class="fa fa-plus-circle"></i> Tambah</button>
            </div> --}}
            <div class="box-body table-responsive">
                <table class="table table-stiped table-bordered">
                    <thead>
                        <th width="5%">No</th>
                        <th>No Nota</th>
                        <th>Cabang</th>
                        <th>Nama Barang</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        {{-- <th>Jenis Retur</th> --}}
                        <th width="15%"><i class="fa fa-cog"></i></th>
                    </thead>
                    <tbody>
                        @php $i = 1; @endphp
                        @foreach ($data as $data)
                        <tr>
                            <td>{{ $i }}</td>
                            {{-- <td>{{ tanggal_indonesia($data->retur_date) }}</td> --}}
                            <td>{{ $data->no_nota }}</td>
                            <td>{{ $data->nama_cabang }}</td>
                            <td>{{ $data->nama_produk }}</td>
                            <td>Rp. {{ format_uang($data->subtotal) }}</td>
                            <td>{{ $data->jumlah }}</td>
                            {{-- <td>{{ $data->jenis_retur }}</td> --}}
                            <td>
                               <a href="/retur/create?id={{ $data->id_penjualan_detail }}" class="btn btn-success">Retur</a>
                            </td>
                            @php $i ++;  @endphp
                        </tr>
                        @endforeach
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


@endsection

@push('scripts')
<script>
    let table;

    $(function () {
        table = $('.table').DataTable({
            responsive: true,
            processing: true,
            serverSide: false,
            autoWidth: false
        });

        // $('#modal-form').validator().on('submit', function (e) {
        //     if (! e.preventDefault()) {
        //         $.post($('#modal-form form').attr('action'), $('#modal-form form').serialize())
        //             .done((response) => {
        //                 $('#modal-form').modal('hide');
        //                 table.ajax.reload();
        //             })
        //             .fail((errors) => {
        //                 alert('Tidak dapat menyimpan data');
        //                 return;
        //             });
        //     }
        // });
    });

    function addForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Cek Nota');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('post');
        $('#modal-form [name=no_nota]').focus();
    }

    function editForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Edit Kategori');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('put');
        $('#modal-form [name=nama_kategori]').focus();

        $.get(url)
            .done((response) => {
                $('#modal-form [name=nama_cabang]').val(response.nama_cabang);
                $('#modal-form [name=alamat_cabang]').val(response.alamat_cabang);
            })
            .fail((errors) => {
                alert('Tidak dapat menampilkan data');
                return;
            });
    }

    function deleteData(url) {
        if (confirm('Yakin ingin menghapus data terpilih?')) {
            $.post(url, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'delete'
                })
                .done((response) => {
                    table.ajax.reload();
                })
                .fail((errors) => {
                    alert('Tidak dapat menghapus data');
                    return;
                });
        }
    }
</script>
@endpush