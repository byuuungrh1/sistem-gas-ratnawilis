<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class barang extends Model
{
    use HasFactory;
    protected $table = "barangs";
    protected $fillable = [
        'nama_barang',
        'harga_jual',
        'harga_beli',
        'sisa_stok',
    ];

    public function pemasukan()
    {
        return $this->hasMany(penjualan::class, 'id_barang');
    }

    public function pengeluaran()
    {
        return $this->hasMany(pembelian::class, 'id_barang');
    }
}
