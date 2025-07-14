<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pelanggans', function (Blueprint $table) {
            $table->id();
            $table->string('nik')->unique(); 
            $table->string('nama_lengkap');
            $table->string('alamat');
            $table->string('no_telepon')->nullable();
            $table->enum('status_pengambilan_gas', ['rumah_tangga', 'umkm'])->default('rumah_tangga');
            // $table->integer('jumlah_pembelian_bulan_ini')->default(0); 
            // $table->date('tanggal_terakhir_beli')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelanggans');
    }
};
