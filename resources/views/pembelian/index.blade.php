@extends('layouts.master')

@section('title')
    Pembelian Barang
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Pembelian Barang</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3 class="text-center">Data Pembelian Barang</h3>
        <div class="box">
            <div class="box-header with-border">
                <button onclick="addForm()" class="btn btn-success btn-flat"><i class="fa fa-plus-circle"></i> Transaksi Pembelian Baru</button>
                @empty(! session('id_pembelian'))
                {{-- <a href="{{ route('pembelian_detail.index') }}" class="btn btn-info btn-xs btn-flat"><i class="fa fa-pencil"></i> Transaksi Aktif</a> --}}
                @endempty
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
                        <label for="">Suplier</label>
                        <select name="suplier" id="" class="form-control">
                            <option value="">Semua Suplier</option>
                            @foreach ($supplier as $item) 
                                <option value="{{ $item->id_supplier }}" @if($item->id_supplier == $_GET['suplier']) selected @endif>{{ $item->nama }}</option>
                            @endforeach
                            
                        </select>
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
                    <a class="btn btn-lg btn-danger"><i class="fa fa-money"></i> Rp. {{ format_uang($harga_pembelian) }}</a>
                </div>
            </form>
        </div>
                
                
                <table class="table table-stiped table-bordered table-pembelian" id="example">
                    <thead>
                        <th width="5%">No</th>
                        <th>Tanggal</th>
                        <th>Cabang</th>
                        <th>Suplier</th>
                        <th>Total Barang</th>
                        <th>Total Harga</th>
                        <th width="15%"><i class="fa fa-cog"></i></th>
                    </thead>
                    <tbody>
                        @php $i = 1; @endphp
                        @foreach ($pembelian as $data)
                        <tr>
                            <td>{{ $i }}</td>
                            <td>{{ tanggal_indonesia($data->tgl_pembelian) }}</td>
                            <td>{{ $data->nama_cabang }}</td>
                            <td>{{ $data->nama }}</td>
                            <td>{{ $data->total_item }}</td>
                            <td>Rp. {{ format_uang($data->total_harga) }}</td>
                            <td>
                                <a onclick="showDetail('{{ route('pembelian.show', $data->id_pembelian) }}')" class="btn btn-xs btn-info btn-flat"><i class="fa fa-eye"></i></a>
                                @if(auth()->user()->user_cabang == 0) 
                                <button onclick="deleteData('{{ route('pembelian.destroy', $data->id_pembelian) }}')" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
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

@includeIf('pembelian.supplier')
@includeIf('pembelian.detail')
@endsection

@push('scripts')
<script>
    let table, table1;

    $(function () {
        table = $('.table-pembelian').DataTable({
            responsive: true,
            processing: true,
            serverSide: false,
            autoWidth: false,
        });

        $('.table-supplier').DataTable();
        table1 = $('.table-detail').DataTable({
            processing: true,
            bSort: false,
            dom: 'Brt',
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'kode_produk'},
                {data: 'nama_produk'},
                {data: 'harga_beli'},
                {data: 'jumlah'},
                {data: 'subtotal'},
            ]
        })
    });

    function addForm() {
        $('#modal-supplier').modal('show');
    }

    function showDetail(url) {
        $('#modal-detail').modal('show');

        table1.ajax.url(url);
        table1.ajax.reload();
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