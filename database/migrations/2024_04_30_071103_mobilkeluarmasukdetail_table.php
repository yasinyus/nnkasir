<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MobilkeluarmasukdetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobil_keluarmasukdetail', function (Blueprint $table) {
            $table->increments('id_keluarmasukdetail');
            $table->integer('id_keluarmasuk')->nullable();
            $table->integer('id_mobil')->nullable();
            $table->date('tanggal_trans')->nullable();
            $table->string('jenis_detail')->nullable();
            $table->integer('uraian')->nullable();
            $table->integer('biaya')->nullable();
            $table->integer('dibayar_detail')->nullable();
            $table->integer('kurang_detail')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
