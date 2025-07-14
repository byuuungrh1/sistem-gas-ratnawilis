<?php

namespace App\Filament\Widgets;

use App\Models\penjualan;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;


class PenjualanChart extends ChartWidget
{
    protected static ?string $heading = 'Data Penjualan';
    protected static string $color = 'info';
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        // Ambil total penjualan per bulan untuk 12 bulan terakhir
        $penjualanPerBulan = penjualan::selectRaw('MONTH(tanggal) as bulan, SUM(subtotal) as total')
            ->whereYear('tanggal', now()->year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');

        // Siapkan data untuk 12 bulan (Jan-Des)
        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $data[] = $penjualanPerBulan[$i] ?? 0;  
        }

        return [
            'datasets' => [
                [
                    'label' => 'Penjualan',
                    'data' => $data,
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#9BD0F5',
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }


    protected function getType(): string
    {
        return 'line';
    }
}
