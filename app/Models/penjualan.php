<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\laba_rugi;   


class penjualan extends Model
{
    use HasFactory;
    protected $table = 'penjualans';

    protected $fillable = [
        'tanggal',
        'id_barang',
        'qty',
        // 'harga',
        'subtotal',     
        'sumber',
        'keterangan',   
        'pelanggan_id',
    ];

    public function barang()
    {
        return $this->belongsTo(barang::class, 'id_barang');
    }

    public function pelanggan()
    {
        return $this->belongsTo(pelanggan::class, 'pelanggan_id');
    }

    public function labaRugi()
    {
        return $this->hasOne(laporan::class, 'id_penjualan');
    }

    protected static function booted()
    {
        static::saving(function ($penjualan) {
            // Ambil harga_jual dari relasi barang
            $barang = barang::find($penjualan->id_barang);
            if ($barang) {
                // Cek stok cukup
                if ($barang->sisa_stok < $penjualan->qty) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'qty' => 'Stok barang tidak mencukupi untuk penjualan ini.'
                    ]);
                }
                $penjualan->subtotal = $penjualan->qty * $barang->harga_jual;
            }
        });
        static::created(function ($penjualan) {
            $barang = barang::find($penjualan->id_barang);
            if ($barang) {
                $barang->sisa_stok -= $penjualan->qty;
                $barang->save();
            }
        });

    }
}
