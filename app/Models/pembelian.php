<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

class pembelian extends Model
{
    use HasFactory;//SoftDeletes;
    protected $table = 'pembelians';

    protected $fillable = [
        'tanggal',
        'id_barang',
        'qty',
        // 'harga',
        'subtotal',
    ];

    public function barang()
    {
        return $this->belongsTo(barang::class, 'id_barang');
    }

    public function labaRugi()
    {
        return $this->hasOne(laporan::class, 'id_pembelian');
    }

    protected static function booted()
    {
        static::saving(function ($pembelian) {
            // Ambil harga_beli dari relasi barang
            $barang = barang::find($pembelian->id_barang);
            if ($barang) {
                $pembelian->subtotal = $pembelian->qty * $barang->harga_beli;
            }
        });
        static::created(function ($pembelian) {
            $barang = barang::find($pembelian->id_barang);
            if ($barang) {
                $barang->sisa_stok += $pembelian->qty;
                $barang->save();
            }
        });
    }

}
