<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;


class SisaStokOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    { 
        $sisaStok3kg = DB::table('barangs')->where('nama_barang', 'Gas LPG 3KG')->sum('sisa_stok');
        $sisaStok12kg = DB::table('barangs')->where('nama_barang', 'Gas LPG 12KG')->sum('sisa_stok');
        $totalSisaStok = DB::table(table: 'barangs')->sum('sisa_stok');

        return [
            Stat::make('Sisa Stok Total', value: $totalSisaStok),
            Stat::make('Sisa Stok 3KG', $sisaStok3kg),
            Stat::make('Sisa Stok 12KG', $sisaStok12kg),
        ];

    }
}
