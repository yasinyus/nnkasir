@extends('layouts.master')

@section('title')
    Daftar Penjualan
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Daftar Penjualan</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3 class="text-center">Data Penjualan Barang</h3>
        <div class="box">
            <div class="box-header with-border">
                
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
                    <label for="">Total Pendapatan</label><br>
                    <a class="btn btn-lg btn-danger"><i class="fa fa-money"></i> Rp. {{ format_uang($total_pendapatan) }}</a>
                </div>
            </form>
        </div>
           
            <div class="box-body table-responsive">
                <table class="table table-stiped table-bordered table-penjualan">
                    <thead>
                        <th width="5%">No</th>
                        <th>Tanggal</th>
                        <th>Cabang</th>
                        <th>Pelanggan</th>
                        <th>Total Item</th>
                        <th>Total Harga</th>
                        {{-- <th>Diskon</th> --}}
                        {{-- <th>Total Bayar</th> --}}
                        <th>Kasir</th>
                        <th width="15%"><i class="fa fa-cog"></i></th>
                    </thead>
                    <tbody>
                        @php $i = 1; @endphp
                        @foreach ($penjualan as $data)
                        <tr>
                            <td>{{ $i }}</td>
                            <td>{{ tanggal_indonesia($data->tgl_penjualan) }}</td>
                            <td>{{ $data->nama_cabang }}</td>
                            <td>{{ $data->nama }}</td>
                            <td>{{ $data->total_item }}</td>
                            <td>Rp. {{ format_uang($data->total_harga) }}</td>
                            <td>{{ $data->name }}</td>
                            <td>
                                <a onclick="showDetail('{{ route('penjualan.show', $data->id_penjualan) }}')" class="btn btn-xs btn-info btn-flat"><i class="fa fa-eye"></i></a>
                                @if(auth()->user()->user_cabang == 0) 
                                <button onclick="deleteData('{{ route('penjualan.destroy', $data->id_penjualan) }}')" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                                @else  @endif
                                <button onclick="notaBesar('{{ route('transaksi.nota_besar_tabel', $data->id_penjualan) }}')" class="btn btn-xs btn-success btn-flat"><i class="fa fa-print"></i> Besar</button>
                                <button onclick="notaKecil('{{ route('transaksi.nota_kecil_tabel', $data->id_penjualan) }}')" class="btn btn-xs btn-warning btn-flat"><i class="fa fa-print"></i> Kecil</button>
                            </td>
                            @php $i ++;  @endphp
                        </tr>
                        @endforeach
                        
                    </tbody>
                </table>
            </div>
    </div>
</div>

@includeIf('penjualan.detail')
@endsection

@push('scripts')
<script>
    let table, table1;

    $(function () {
        table = $('.table-penjualan').DataTable({
            responsive: true,
            processing: true,
            serverSide: false,
            autoWidth: false,
        });

        table1 = $('.table-detail').DataTable({
            processing: true,
            bSort: false,
            dom: 'Brt',
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'kode_produk'},
                {data: 'nama_produk'},
                {data: 'harga_jual'},
                {data: 'jumlah'},
                {data: 'subtotal'},
            ]
        })
    });

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
@push('scripts')
<script>
    // tambahkan untuk delete cookie innerHeight terlebih dahulu
    document.cookie = "innerHeight=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    
    function notaKecil(url, title) {
        popupCenter(url, title, 625, 500);
    }

    function notaBesar(url, title) {
        popupCenter(url, title, 900, 675);
    }

    function popupCenter(url, title, w, h) {
        const dualScreenLeft = window.screenLeft !==  undefined ? window.screenLeft : window.screenX;
        const dualScreenTop  = window.screenTop  !==  undefined ? window.screenTop  : window.screenY;

        const width  = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
        const height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

        const systemZoom = width / window.screen.availWidth;
        const left       = (width - w) / 2 / systemZoom + dualScreenLeft
        const top        = (height - h) / 2 / systemZoom + dualScreenTop
        const newWindow  = window.open(url, title, 
        `
            scrollbars=yes,
            width  = ${w / systemZoom}, 
            height = ${h / systemZoom}, 
            top    = ${top}, 
            left   = ${left}
        `
        );

        if (window.focus) newWindow.focus();
    }
</script>
@endpush