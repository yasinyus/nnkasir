<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMobilTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobil', function (Blueprint $table) {
            $table->increments('id_mobil');
            $table->date('tanggal')->nullable();
            $table->string('nopol')->nullable();
            $table->string('type')->nullable();
            $table->string('nama_pemilik')->nullable();
            $table->string('telp')->nullable();
            $table->string('keluhan')->nullable();
            // $table->string('jenis')->nullable();
            // $table->string('uraian')->nullable();
            // $table->integer('jumlah')->nullable();
            // $table->integer('biaya')->nullable();
            // $table->integer('dibayar')->nullable();
            // $table->integer('kurang')->nullable();
            $table->string('operator')->nullable();
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
        Schema::dropIfExists('mobil');
    }
}
