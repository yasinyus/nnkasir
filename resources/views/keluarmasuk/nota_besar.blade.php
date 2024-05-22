<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Nota PDF</title>

    <style>
        table td {
            /* font-family: Arial, Helvetica, sans-serif; */
            font-size: 14px;
        }
        table.data td,
        table.data th {
            border: 1px solid #ccc;
            padding: 5px;
        }
        table.data {
            border-collapse: collapse;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <table width="100%">
        <tr>
            <td rowspan="4" width="60%">
                <img src="{{ public_path($setting->path_logo) }}" alt="{{ $setting->path_logo }}" width="120">
                <br>
                {{ $setting->alamat }}
                <br>
                <br>
            </td>
            <td>Tanggal</td>
            <td>: {{ tanggal_indonesia($keluarmasuk->tanggal_service) }}</td>
        </tr>
        <tr>
            <td>Nama Pelanggan</td>
            <td>: {{ $mobil->nama_pemilik ?? '' }}</td>
        </tr>
        <tr>
            <td>Nopol / Jenis</td>
            <td>: {{ $mobil->nopol ." / ". $mobil->type ?? '' }}</td>
        </tr>
    </table>

    <table class="data" width="100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Jenis</th>
                <th>Uraian</th>
                <th>Biaya</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detail as $key => $item)
                <tr>
                    <td class="text-center">{{ $key+1 }}</td>
                    <td>{{ $item->jenis_detail }}</td>
                    <td>{{ $item->uraian }}</td>
                    <td class="text-right">{{ format_uang($item->biaya) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right"><b>Total Harga</b></td>
                <td class="text-right"><b>{{ format_uang($keluarmasuk->total_harga) }}</b></td>
            </tr>
            <tr>
                <td colspan="3" class="text-right"><b>Diterima</b></td>
                <td class="text-right"><b>{{ format_uang($keluarmasuk->bayar) }}</b></td>
            </tr>
            <tr>
                <td colspan="3" class="text-right"><b>Kembali</b></td>
                <td class="text-right"><b>{{ format_uang($keluarmasuk->bayar - $keluarmasuk->total_harga) }}</b></td>
            </tr>
        </tfoot>
    </table>

    <table width="100%">
        <tr>
            <td><b>Terimakasih telah berbelanja dan sampai jumpa</b></td>
            <td class="text-center">
                Kasir
                <br>
                <br>
                {{ auth()->user()->name }}
            </td>
        </tr>
    </table>
</body>
</html>