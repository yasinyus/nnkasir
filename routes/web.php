<?php

use App\Http\Controllers\{
    CabangController,
    DashboardController,
    KategoriController,
    KategorijasaController,
    LaporanController,
    ProdukController,
    MobilController,
    MemberController,
    MobilkeluarmasukController,
    MobilkeluarmasukDetailController,
    PengeluaranController,
    PembelianController,
    PembelianDetailController,
    PenjualanController,
    PenjualanDetailController,
    ReturController,
    SettingController,
    SupplierController,
    UserController,
};
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::group(['middleware' => 'level:1,2'], function () {
        Route::get('/kategori/data', [KategoriController::class, 'data'])->name('kategori.data');
        Route::resource('/kategori', KategoriController::class);

        Route::get('/cabang/data', [CabangController::class, 'data'])->name('cabang.data');
        Route::resource('/cabang', CabangController::class);

        Route::post('/retur/data', [ReturController::class, 'data'])->name('retur.data');
        Route::resource('/retur', ReturController::class);

        Route::get('/kategorijasa/data', [KategorijasaController::class, 'data'])->name('kategorijasa.data');
        Route::resource('/kategorijasa', KategorijasaController::class);

        Route::get('/produk/data', [ProdukController::class, 'data'])->name('produk.data');
        Route::post('/produk/delete-selected', [ProdukController::class, 'deleteSelected'])->name('produk.delete_selected');
        Route::post('/produk/cetak-barcode', [ProdukController::class, 'cetakBarcode'])->name('produk.cetak_barcode');
        Route::get('/produk/export_produk', [ProdukController::class, 'export_produk'])->name('produk.export_produk');
        Route::post('/produk/import_produk', [ProdukController::class, 'import_produk'])->name('produk.import_produk');
        Route::post('/produk/edit_stok', [ProdukController::class, 'edit_stok'])->name('produk.edit_stok');
        Route::resource('/produk', ProdukController::class);

        Route::get('/mobil/data', [MobilController::class, 'data'])->name('mobil.data');
        Route::post('/mobil/delete-selected', [MobilController::class, 'deleteSelected'])->name('mobil.delete_selected');
        Route::post('/mobil/cetak-barcode', [MobilController::class, 'cetakBarcode'])->name('mobil.cetak_barcode');
        Route::resource('/mobil', MobilController::class);

        Route::get('/member/data', [MemberController::class, 'data'])->name('member.data');
        Route::post('/member/cetak-member', [MemberController::class, 'cetakMember'])->name('member.cetak_member');
        Route::resource('/member', MemberController::class);

        Route::get('/supplier/data', [SupplierController::class, 'data'])->name('supplier.data');
        Route::resource('/supplier', SupplierController::class);

        Route::get('/pengeluaran/data', [PengeluaranController::class, 'data'])->name('pengeluaran.data');
        Route::resource('/pengeluaran', PengeluaranController::class);

        Route::get('/pembelian/data', [PembelianController::class, 'data'])->name('pembelian.data');
        Route::get('/pembelian/{id}/create', [PembelianController::class, 'create'])->name('pembelian.create');
        Route::resource('/pembelian', PembelianController::class)
            ->except('create');

        Route::get('/keluarmasuk/data', [MobilkeluarmasukController::class, 'data'])->name('keluarmasuk.data');
        Route::get('/keluarmasuk/{id}/create', [MobilkeluarmasukController::class, 'create'])->name('keluarmasuk.create');
        Route::resource('/keluarmasuk', MobilkeluarmasukController::class)
            ->except('create');

        Route::get('/keluarmasuk_detail/{id}/data', [MobilkeluarmasukDetailController::class, 'data'])->name('keluarmasuk_detail.data');
        Route::get('/edit_detail/{id}/{id_mobil}', [MobilkeluarmasukDetailController::class, 'edit_detail'])->name('edit_detail');
        Route::get('/keluarmasuk_detail/loadform/{diskon}/{total}', [MobilkeluarmasukDetailController::class, 'loadForm'])->name('keluarmasuk_detail.load_form');
        Route::resource('/keluarmasuk_detail', MobilkeluarmasukDetailController::class)->except('create', 'show', 'edit');
        Route::get('/nota_mobil_keluarmasuk/{id}', [MobilkeluarmasukController::class, 'nota_mobil_keluarmasuk'])->name('nota_mobil_keluarmasuk');

        Route::get('/pembelian_detail/{id}/data', [PembelianDetailController::class, 'data'])->name('pembelian_detail.data');
        Route::get('/pembelian_detail/loadform/{diskon}/{total}', [PembelianDetailController::class, 'loadForm'])->name('pembelian_detail.load_form');
        Route::resource('/pembelian_detail', PembelianDetailController::class)
            ->except('create', 'show', 'edit');

        Route::get('/penjualan/data', [PenjualanController::class, 'data'])->name('penjualan.data');
        Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
        Route::get('/penjualan/{id}', [PenjualanController::class, 'show'])->name('penjualan.show');
        Route::delete('/penjualan/{id}', [PenjualanController::class, 'destroy'])->name('penjualan.destroy');
    });

    Route::group(['middleware' => 'level:1,2'], function () {
        Route::get('/transaksi/baru', [PenjualanController::class, 'create'])->name('transaksi.baru');
        Route::post('/transaksi/simpan', [PenjualanController::class, 'store'])->name('transaksi.simpan');
        Route::get('/transaksi/selesai', [PenjualanController::class, 'selesai'])->name('transaksi.selesai');
        Route::get('/transaksi/nota-kecil', [PenjualanController::class, 'notaKecil'])->name('transaksi.nota_kecil');
        Route::get('/transaksi/nota-kecil-tabel/{id}', [PenjualanController::class, 'notaKecil_tabel'])->name('transaksi.nota_kecil_tabel');
        Route::get('/transaksi/nota-besar', [PenjualanController::class, 'notaBesar'])->name('transaksi.nota_besar');
        Route::get('/transaksi/nota-besar-tabel/{id}', [PenjualanController::class, 'notaBesar_tabel'])->name('transaksi.nota_besar_tabel');

        Route::get('/transaksi/{id}/data', [PenjualanDetailController::class, 'data'])->name('transaksi.data');
        Route::put('/transaksi_update_harga/{id}', [PenjualanDetailController::class, 'update_harga'])->name('transaksi_update_harga.update_harga');
        Route::get('/transaksi/loadform/{diskon}/{total}/{diterima}', [PenjualanDetailController::class, 'loadForm'])->name('transaksi.load_form');
        Route::resource('/transaksi', PenjualanDetailController::class)
            ->except('create', 'show', 'edit');
    });

    Route::group(['middleware' => 'level:1,2'], function () {
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/data/{awal}/{akhir}', [LaporanController::class, 'data'])->name('laporan.data');
        Route::get('/laporan/pdf/{awal}/{akhir}', [LaporanController::class, 'exportPDF'])->name('laporan.export_pdf');

        Route::get('/user/data', [UserController::class, 'data'])->name('user.data');
        Route::resource('/user', UserController::class);

        Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
        Route::get('/setting/first', [SettingController::class, 'show'])->name('setting.show');
        Route::post('/setting', [SettingController::class, 'update'])->name('setting.update');
    });
 
    Route::group(['middleware' => 'level:1,2'], function () {
        Route::get('/profil', [UserController::class, 'profil'])->name('user.profil');
        Route::post('/profil', [UserController::class, 'updateProfil'])->name('user.update_profil');
    });
});