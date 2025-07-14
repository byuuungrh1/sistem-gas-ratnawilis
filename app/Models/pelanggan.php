<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggans';

    protected $fillable = [
        'nama_lengkap',
        'nik',
        'no_telepon',
        'alamat',
        'status_pengambilan_gas',
        // 'jumlah_pembelian_bulan_ini',
        // 'tanggal_terakhir_beli',
    ];

    public function pemasukan()
    {
        return $this->hasMany(penjualan::class, 'pelanggan_id');
    }
}
