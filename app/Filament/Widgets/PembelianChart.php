<?php

namespace App\Filament\Widgets;

use App\Models\pembelian;
use Filament\Widgets\ChartWidget;

class PembelianChart extends ChartWidget
{
    protected static ?string $heading = 'Data Pembelian';
    protected static string $color = 'info';
    protected static ?int $sort = 5;

    protected function getData(): array
    {
        // Ambil total pembelian per bulan untuk 12 bulan terakhir
        $pembelianPerBulan = pembelian::selectRaw('MONTH(tanggal) as bulan, SUM(subtotal) as total')
            ->whereYear('tanggal', now()->year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');

        // Siapkan data untuk 12 bulan (Jan-Des)
        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $data[] = $pembelianPerBulan[$i] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pembelian',
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
