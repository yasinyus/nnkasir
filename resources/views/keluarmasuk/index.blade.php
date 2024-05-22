@extends('layouts.master')

@section('title')
    Daftar Mobil Keluar Masuk
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Daftar Mobil Keluar Masuk</li>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3 class="text-center">Mobil Keluar Masuk</h3>
        <div class="box">
            <div class="box-header with-border">
                <button onclick="addForm()" class="btn btn-success btn-flat"><i class="fa fa-plus-circle"></i> Transaksi Baru</button>
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
                        <label for="">Status</label>
                        <select name="status" id="" class="form-control">
                            <option value="">Semua Status</option>
                            <option  {{ $_GET['status'] == "Dalam Perbaikan" ? "selected" : "" }}>Dalam Perbaikan</option>
                            <option  {{ $_GET['status'] == "Telah Dikeluarkan" ? "selected" : "" }}>Telah Dikeluarkan</option>
                        </select>
                   
                </div>
                <div class="col-md-2">
                        <label for="">Waktu</label>
                        <select name="waktu" id="" class="form-control">
                            <option value="">Semua Waktu</option>
                            <option value="30" {{ $_GET['waktu'] == 30 ? "selected" : "" }}>1 Bulan Terakhir</option>
                            <option value="60" {{ $_GET['waktu'] == 60 ? "selected" : "" }}>2 Bulan Terakhir</option>
                            <option value="90" {{ $_GET['waktu'] == 90 ? "selected" : "" }}>3 Bulan Terakhir</option>
                            <option value="120" {{ $_GET['waktu'] == 120 ? "selected" : "" }}>4 Bulan Terakhir</option>
                            <option value="180" {{ $_GET['waktu'] == 180 ? "selected" : "" }}>6 Bulan Terakhir</option>
                            <option value="360" {{ $_GET['waktu'] == 360 ? "selected" : "" }}>1 Tahun Terakhir</option>
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
                    <label for="">Pendapatan</label><br>
                    <a class="btn btn-lg btn-danger"><i class="fa fa-money"></i> Rp. {{ format_uang($pendapatan_total) }}</a>
                </div>
            </form>
                </div>
                
                
                <table class="table table-stiped table-bordered table-pembelian" id="example">
                    <thead>
                        <th width="5%">No</th>
                        <th>Tanggal</th>
                        <th>Cabang</th>
                        <th>Nopol</th>
                        <th>Jenis Mobil</th>
                        <th>Nama Pemilik</th>
                        <th>Total Biaya</th>
                        <th>Status</th>
                        <th width="15%"><i class="fa fa-cog"></i></th>
                    </thead>
                    <tbody>
                        @php $i = 1; @endphp
                        @foreach ($keluarmasuk_akhir as $data)
                        <tr>
                            <td>{{ $i }}</td>
                            <td>{{ tanggal_indonesia($data->tanggal_service) }}</td>
                            <td>{{ $data->nama_cabang }}</td>
                            <td>{{ $data->nopol }}</td>
                            <td>{{ $data->type }}</td>
                            <td>{{ $data->nama_pemilik }}</td>
                            <td>Rp. {{ format_uang($data->total_harga) }}</td>
                            <td>{{ $data->status_trans }}</td>
                            <td>
                                <a href="/edit_detail/{{ $data->id_keluarmasuk }}/{{ $data->id_mobil }}" class="btn btn-xs btn-primary btn-flat">Lihat</a>
                                @if(auth()->user()->user_cabang == 0) 
                                <button onclick="deleteData('{{ route('keluarmasuk.destroy', $data->id_keluarmasuk) }}')" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                                @else  @endif{{-- <button onclick="deleteData(route('keluarmasuk.destroy', $data->id_keluarmasuk))" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button> --}}
                                <button onclick="notaBesar('{{  route('nota_mobil_keluarmasuk', $data->id_keluarmasuk) }}' )" class="btn btn-xs btn-success btn-flat"><i class="fa fa-print"></i></button>
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

@includeIf('keluarmasuk.supplier')
@includeIf('keluarmasuk.detail')
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
    });

    $('#modal-form').validator().on('submit', function (e) {
        if (! e.preventDefault()) {
            $.post($('#modal-form form').attr('action'), $('#modal-form form').serialize())
                .done((response) => {
                    $('#modal-form').modal('hide');
                    table.ajax.reload();
                })
                .fail((errors) => {
                    alert('Tidak dapat menyimpan data');
                    return;
                });
        }
    });
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
                    location.reload()
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