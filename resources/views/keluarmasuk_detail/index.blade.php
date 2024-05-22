@extends('layouts.master')

@section('title')
    Transaksi Mobil Keluar Masuk
@endsection

@push('css')
<style>
    .tampil-bayar {
        font-size: 5em;
        text-align: center;
        height: 100px;
    }

    .tampil-terbilang {
        padding: 10px;
        background: #f0f0f0;
    }

    .table-pembelian tbody tr:last-child {
        display: none;
    }

    @media(max-width: 768px) {
        .tampil-bayar {
            font-size: 3em;
            height: 70px;
            padding-top: 5px;
        }
    }
</style>
@endpush

@section('breadcrumb')
    @parent
    <li class="active">Transaksi Mobil Keluar Masuk</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <table>
                    <tr>
                        <td>Nopol</td>
                        <td>: {{ $mobil->nopol }}</td>
                    </tr>
                    <tr>
                        <td>Jenis Mobil</td>
                        <td>: {{ $mobil->type }}</td>
                    </tr>
                    <tr>
                        <td>Nama Pemilik</td>
                        <td>: {{ $mobil->nama_pemilik }}</td>
                    </tr>
                    <tr>
                        <td>Telp</td>
                        <td>: {{ $mobil->telp }}</td>
                    </tr>
                </table>
            </div>
            <div class="box-body">
                    
                <form class="form-produk">
                    @csrf
                    <div class="form-group row">
                        <div class="col-lg-5">
                            <div class="input-group">
                                <input type="hidden" name="id_keluarmasuk" id="id_keluarmasuk" value="{{ $id_keluarmasuk }}">
                                {{-- <input type="hidden" name="id_produk" id="id_produk"> --}}
                                <span class="input-group-btn">
                                    <button onclick="tampilProduk()" class="btn btn-danger btn-flat" type="button"><i class="fa fa-arrow-right"></i> Tambah Uraian</button>
                                </span>
                            </div>
                        </div>
                    </div>
                </form>

                <table class="table table-stiped table-bordered table-pembelian">
                    <thead>
                        <th width="5%">No</th>
                        <th>Jenis</th>
                        <th>Uraian</th>
                        <th>Biaya</th>
                        <th width="15%"><i class="fa fa-cog"></i></th>
                    </thead>
                </table>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="tampil-bayar bg-primary"></div>
                        <div class="tampil-terbilang"></div>
                    </div>
                    <div class="col-lg-4">
                        <form action="{{ route('keluarmasuk.store') }}" class="form-pembelian" method="post">
                            @csrf
                            @method('post')
                            <input type="hidden" name="id_keluarmasuk" value="{{ $id_keluarmasuk }}">
                            <input type="hidden" name="id_mobil" value="{{ $mobil->id_mobil }}">
                            <input type="hidden" name="total_harga" value="{{ $total }}">
                            {{-- <input type="hidden" name="bayar" id="bayar">
                            <input type="hidden" name="diterima" id="diterima"> --}}

                            <div class="form-group row">
                                <label for="totalrp" class="col-lg-2 control-label">Total</label>
                                <div class="col-lg-8">
                                    <input type="text"   class="form-control" value="Rp. {{ format_uang($total) }}" readonly>
                                </div>
                            </div>
                            {{-- <div class="form-group row">
                                <label for="diskon" class="col-lg-2 control-label">Diskon</label>
                                <div class="col-lg-8">
                                    <input type="number" name="diskon" id="diskon" class="form-control" value="{{ $diskon }}">
                                </div>
                            </div> --}}
                            <div class="form-group row">
                                <label for="bayar" class="col-lg-2 control-label">Bayar</label>
                                <div class="col-lg-8">
                                    <input type="text" name="bayar"  class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="bayar" class="col-lg-2 control-label">Status</label>
                                <div class="col-lg-8">
                                    <select name="status_trans" class="form-control">
                                        <option @if($status->status_trans == "Dalam Perbaikan") selected @endif }}>Dalam Perbaikan</option>
                                        <option @if($status->status_trans == "Telah Dikeluarkan") selected @endif }}>Telah Dikeluarkan</option>
                                    </select>
                                </div>
                            </div>
                        
                    </div>
                </div>
            </div>

            <div class="box-footer">
                <button type="submit" class="btn btn-primary btn-sm btn-flat pull-right"><i class="fa fa-floppy-o"></i> Simpan Transaksi</button>
            </div>
        </form>
        </div>
    </div>
</div>

@includeIf('keluarmasuk_detail.produk')
@endsection

@push('scripts')
<script>
    let table, table2;

    $(function () {
        $('body').addClass('sidebar-collapse');

        table = $('.table-pembelian').DataTable({
            responsive: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('keluarmasuk_detail.data', $id_keluarmasuk) }}',
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'jenis_detail'},
                {data: 'uraian'},
                {data: 'biaya'},
                {data: 'aksi', searchable: false, sortable: false},
            ],
            dom: 'Brt',
            bSort: false,
            paginate: false
        })
        .on('draw.dt', function () {
            loadForm($('#diskon').val());
        });
        table2 = $('.table-produk').DataTable();

        $(document).on('input', '.quantity', function () {
            let id = $(this).data('id');
            let jumlah = parseInt($(this).val());

            if (jumlah < 1) {
                $(this).val(1);
                alert('Jumlah tidak boleh kurang dari 1');
                return;
            }
            if (jumlah > 10000) {
                $(this).val(10000);
                alert('Jumlah tidak boleh lebih dari 10000');
                return;
            }

            $.post(`{{ url('/pembelian_detail') }}/${id}`, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'put',
                    'jumlah': jumlah
                })
                .done(response => {
                    $(this).on('mouseout', function () {
                        table.ajax.reload(() => loadForm($('#diskon').val()));
                    });
                })
                .fail(errors => {
                    alert('Tidak dapat menyimpan data');
                    return;
                });
        });

        $(document).on('input', '#diskon', function () {
            if ($(this).val() == "") {
                $(this).val(0).select();
            }

            loadForm($(this).val());
        });

        $('.btn-simpan').on('click', function () {
            $('.form-pembelian').submit();
        });
    });

    function tampilProduk() {
        $('#modal-produk').modal('show');
    }

    function hideProduk() {
        $('#modal-produk').modal('hide');
    }

    function pilihProduk(id, kode) {
        $('#id_produk').val(id);
        $('#kode_produk').val(kode);
        hideProduk();
        tambahProduk();
    }

    function tambahProduk() {
        $.post('{{ route('pembelian_detail.store') }}', $('.form-produk').serialize())
            .done(response => {
                $('#kode_produk').focus();
                table.ajax.reload(() => loadForm($('#diskon').val()));
            })
            .fail(errors => {
                alert('Tidak dapat menyimpan data');
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
                    location.reload()
                })
                .fail((errors) => {
                    alert('Tidak dapat menghapus data');
                    return;
                });
        }
    }

    // function loadForm(diskon = 0) {
    //     $('#total').val($('.total').text());
    //     $('#total_item').val($('.total_item').text());

    //     $.get(`{{ url('/keluarmasuk_detail/loadform') }}/${diskon}/${$('.total').text()}`)
    //         .done(response => {
    //             $('#totalrp').val('Rp. '+ response.totalrp);
    //             $('#bayarrp').val('Rp. '+ response.bayarrp);
    //             $('#bayar').val(response.bayar);
    //             $('.tampil-bayar').text('Rp. '+ response.bayarrp);
    //             $('.tampil-terbilang').text(response.terbilang);
    //         })
    //         .fail(errors => {
    //             alert('Tidak dapat menampilkan data');
    //             return;
    //         })
    // }
</script>
@endpush