<?php

namespace App\Filament\Resources\LaporanPenjualanResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use App\Models\penjualan;

class ViewLaporanPenjualan extends ViewRecord
{
    public function getRecord(): \Illuminate\Database\Eloquent\Model
    {
        $tanggal = $this->getRecordKey();
        $model = new penjualan();
        $model->tanggal = $tanggal;
        return $model;
    }
}
