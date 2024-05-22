<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MobilkeluarmasukTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobil_keluarmasuk', function (Blueprint $table) {
            $table->increments('id_keluarmasuk');
            $table->integer('id_mobil')->nullable();
            $table->date('tanggal_service')->nullable();
            $table->string('jenis')->nullable();
            $table->integer('total_harga')->nullable();
            $table->integer('bayar')->nullable();
            $table->integer('diterima')->nullable();
            $table->integer('kurang')->nullable();
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
