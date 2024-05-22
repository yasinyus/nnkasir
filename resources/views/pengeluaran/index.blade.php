@extends('layouts.master')

@section('title')
    Daftar Pengeluaran
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Daftar Pengeluaran</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3 class="text-center">Data Pengeluaran</h3>
            <div class="box">
                <div class="box-header with-border">
                    <button onclick="addForm('{{ route('pengeluaran.store') }}')" class="btn btn-success btn-flat"><i class="fa fa-plus-circle"></i> Pengeluaran Baru</button>
              
                </div>
                <form action="">
                <div class="box-body table-responsive">
                    <div class="row">
                    <div class="col-md-2">
                        
                            <label for="">Tgl awal</label>
                            <input type="date" class="form-control" name="start_date" value="{{ $_GET['start_date'] }}"><br>
                       
                    </div>
                    <div class="col-md-2">
                        
                            <label for="">Tgl akhir</label>
                            <input type="date" class="form-control" name="end_date" value="{{ $_GET['end_date'] }}">
                       
                    </div>
                    <div class="col-md-2">
                        <label for="">Cabang</label>
                        <select name="cabang" id="" class="form-control" @if(auth()->user()->user_cabang != 0) disabled @else @endif>
                            <option value="">Semua Cabang</option>
                            @foreach ($cabang as $item) 
                                @if(auth()->user()->user_cabang != 0)
                                    <option value="{{ $item->id_cabang }}" @if($item->id_cabang == auth()->user()->user_cabang) selected @endif>{{ $item->nama_cabang }}</option>
                                @else
                                    
                                    <option value="{{ $item->id_cabang }}" @if($item->id_cabang == $_GET['cabang']) selected @endif>{{ $item->nama_cabang }} </option>
                                @endif
                                
                            @endforeach
                            
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="">.</label><br>
                        <input type="submit" value="Filter" class="btn btn-success">
                    </div>
                    <div class="col-md-12">
                        <label for="">Total Pembelian</label><br>
                        <a class="btn btn-lg btn-danger"><i class="fa fa-money"></i> Rp. {{ format_uang($total_pengeluaran) }}</a>
                    </div>
                </form>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-stiped table-bordered">
                    <thead>
                        <th width="5%">No</th>
                        <th>Tanggal</th>
                        <th>Cabang</th>
                        <th>Deskripsi</th>
                        <th>Nominal</th>
                        <th width="15%"><i class="fa fa-cog"></i></th>
                    </thead>
                    <tbody>
                        @php $i = 1; @endphp
                        @foreach ($pengeluaran as $data)
                        <tr>
                            <td>{{ $i }}</td>
                            <td>{{ tanggal_indonesia($data->tgl_pengeluaran) }}</td>
                            <td>{{ $data->nama_cabang }}</td>
                            <td>{{ $data->deskripsi }}</td>
                            <td>Rp. {{ format_uang($data->nominal) }}</td>
                            <td>
                                <button type="button" onclick="editForm('{{ route('pengeluaran.update', $data->id_pengeluaran) }}')" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                                @if(auth()->user()->user_cabang == 0) 
                                <button onclick="deleteData('{{ route('pengeluaran.destroy', $data->id_pengeluaran) }}')" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                                @else  @endif
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

@includeIf('pengeluaran.form')
@endsection

@push('scripts')
<script>
    let table;

    $(function () {
        table = $('.table').DataTable({
            responsive: true,
            processing: true,
            serverSide: false,
            autoWidth: false,
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'created_at'},
                {data: 'nama_cabang'},
                {data: 'deskripsi'},
                {data: 'nominal'},
                {data: 'aksi', searchable: false, sortable: false},
            ]
        });

        $('#modal-form').validator().on('submit', function (e) {
            if (! e.preventDefault()) {
                $.post($('#modal-form form').attr('action'), $('#modal-form form').serialize())
                    .done((response) => {
                        $('#modal-form').modal('hide');
                        location.reload(true);
                    })
                    .fail((errors) => {
                        alert('Tidak dapat menyimpan data');
                        return;
                    });
            }
        });
    });

    function addForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Tambah Pengeluaran');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('post');
        $('#modal-form [name=deskripsi]').focus();
    }

    function editForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Edit Pengeluaran');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('put');
        $('#modal-form [name=deskripsi]').focus();

        $.get(url)
            .done((response) => {
                $('#modal-form [name=deskripsi]').val(response.deskripsi);
                $('#modal-form [name=nominal]').val(response.nominal);
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
                    location.reload(true);
                })
                .fail((errors) => {
                    alert('Tidak dapat menghapus data');
                    return;
                });
        }
    }
</script>
@endpush