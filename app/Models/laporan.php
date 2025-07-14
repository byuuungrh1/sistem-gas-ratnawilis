<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class laporan extends Model
{
    use HasFactory;
    protected $table = "laporans";
    protected $fillable = [
        'id_penjualan',
        'tanggal_penjualan',
        'subtotal_penjualan',
        'id_pembelian',
        'tanggal_pembelian',
        'subtotal_pembelian',
        'laba_bersih',
    ];

    public function penjualan()
    {
        return $this->belongsTo(penjualan::class, 'id_penjualan');
    }

    public function pembelian()
    {
        return $this->belongsTo(pembelian::class, 'id_pembelian');
    }
}
