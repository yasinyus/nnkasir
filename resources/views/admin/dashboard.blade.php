@extends('layouts.master')

@section('title')
    Dashboard
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Dashboard</li>
@endsection

@section('content')
<!-- Small boxes (Stat box) -->
<div class="row">
    <div class="col-lg-4 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-green">
            <div class="inner">
                <p>Total Penjualan</p>
                <h3 style="font-size: 22px">Rp. {{ format_uang($total_penjualan_harian) }}</h3>

                <p> Dari {{ $total_transaksi_harian }} Transaksi Penjualan hari ini</p>
            </div>
            <div class="icon">
                <i class="fa fa-cart-plus"></i>
            </div>
            <a href="{{ route('penjualan.index') }}" class="small-box-footer">Lihat <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    
    <div class="col-lg-4 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-purple">
            <div class="inner">
                <p>Total Pembelian Harian</p>
                <h3 style="font-size: 22px">Rp. {{ format_uang($total_pembelian_harian) }}</h3>

                <p>Dari {{ $total_trans_pembelian_harian }} Transaksi Pembelian Barang hari ini</p>
            </div>
            <div class="icon">
                <i class="fa fa-money"></i>
            </div>
            <a href="{{ route('pembelian.index') }}" class="small-box-footer">Lihat <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-4 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-red">
            <div class="inner">
                <p>Pengeluaran Harian</p>
                <h3 style="font-size: 22px">Rp. {{ format_uang($total_pengeluaran_harian) }}</h3>

                <p>Dari {{ $total_trans_pengeluaran_harian }} Transaksi Pengeluaran hari ini</p>
            </div>
            <div class="icon">
                <i class="fa fa-money"></i>
            </div>
            <a href="{{ route('pengeluaran.index') }}" class="small-box-footer">Lihat <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-4 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-green">
            <div class="inner">
                <h3 style="font-size: 22px">{{ $produk }}</h3>

                <p>Total Produk/Barang</p>
            </div>
            <div class="icon">
                <i class="fa fa-cubes"></i>
            </div>
            <a href="{{ route('produk.index') }}" class="small-box-footer">Lihat <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    
    <div class="col-lg-4 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-purple">
            <div class="inner">
                <h3 style="font-size: 22px">{{ $supplier }}</h3>

                <p>Total Supplier</p>
            </div>
            <div class="icon">
                <i class="fa fa-truck"></i>
            </div>
            <a href="{{ route('supplier.index') }}" class="small-box-footer">Lihat <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-4 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-red">
            <div class="inner">
                <h3 style="font-size: 22px">{{ $member }}</h3>

                <p>Konsumen Toko</p>
            </div>
            <div class="icon">
                <i class="fa fa-id-card"></i>
            </div>
            <a href="{{ route('member.index') }}" class="small-box-footer">Lihat <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    
    <!-- ./col -->
</div>
<!-- /.row -->
<!-- Main row -->
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Grafik Pendapatan {{ tanggal_indonesia($tanggal_awal, false) }} s/d {{ tanggal_indonesia($tanggal_akhir, false) }}</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="chart">
                            <!-- Sales Chart Canvas -->
                            <canvas id="salesChart" style="height: 180px;"></canvas>
                        </div>
                        <!-- /.chart-responsive -->
                    </div>
                </div>
                <!-- /.row -->
            </div>
        </div>
        <!-- /.box -->
    </div>
    <!-- /.col -->
</div>
<!-- /.row (main row) -->
@endsection

@push('scripts')
<!-- ChartJS -->
<script src="{{ asset('AdminLTE-2/bower_components/chart.js/Chart.js') }}"></script>
<script>
$(function() {
    // Get context with jQuery - using jQuery's .get() method.
    var salesChartCanvas = $('#salesChart').get(0).getContext('2d');
    // This will get the first returned node in the jQuery collection.
    var salesChart = new Chart(salesChartCanvas);

    var salesChartData = {
        labels: {{ json_encode($data_tanggal) }},
        datasets: [
            {
                label: 'Pendapatan',
                fillColor           : 'rgba(60,141,188,0.9)',
                strokeColor         : 'rgba(60,141,188,0.8)',
                pointColor          : '#3b8bba',
                pointStrokeColor    : 'rgba(60,141,188,1)',
                pointHighlightFill  : '#fff',
                pointHighlightStroke: 'rgba(60,141,188,1)',
                data: {{ json_encode($data_pendapatan) }}
            }
        ]
    };

    var salesChartOptions = {
        pointDot : false,
        responsive : true
    };

    salesChart.Line(salesChartData, salesChartOptions);
});
</script>
@endpush