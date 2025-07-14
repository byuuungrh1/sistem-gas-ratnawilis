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
        Schema::table('pelanggans', function (Blueprint $table) {
            //$table->dropColumn(['jumlah_pembelian_bulan_ini', 'tanggal_terakhir_beli']);
        });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            //$table->integer('jumlah_pembelian_bulan_ini')->default(0); 
            //$table->date('tanggal_terakhir_beli')->nullable();
        });
    }
};
