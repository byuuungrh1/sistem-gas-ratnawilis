<?php

namespace App\Filament\Pages;

use App\Models\pembelian;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Pages\RekapPembelianDetail;
use Carbon\Carbon;

class RekapPembelian extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-down';
    protected static ?string $navigationLabel = 'Pembelian';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.rekap-pembelian';

    protected function getTableQuery(): Builder
    {
        return pembelian::query()
            ->select([
                DB::raw('MIN(id) as id'),
                DB::raw('DATE(tanggal) as tanggal'),
                DB::raw('SUM(subtotal) as total_subtotal'),
            ])
            ->groupBy('tanggal')
            ->orderBy('id');
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('tanggal')->date('d M Y')->label('Tanggal')->sortable(),
            TextColumn::make('total_subtotal')->label('Total Pembelian')->money('IDR', true),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('bulan')
                ->label('Bulan')
                ->options([
                    '1' => 'Januari',
                    '2' => 'Februari',
                    '3' => 'Maret',
                    '4' => 'April',
                    '5' => 'Mei',
                    '6' => 'Juni',
                    '7' => 'Juli',
                    '8' => 'Agustus',
                    '9' => 'September',
                    '10' => 'Oktober',
                    '11' => 'November',
                    '12' => 'Desember',
                ])
                ->default((string) Carbon::now()->month)
                ->query(fn($query, $state) => $state ? $query->whereMonth('tanggal', $state) : null),

            SelectFilter::make('tahun')
                ->label('Tahun')
                ->options(function () {
                    $years = pembelian::selectRaw('YEAR(tanggal) as year')->distinct()->pluck('year')->toArray();
                    return array_combine($years, $years);
                })
                ->default((string) Carbon::now()->year)
                ->query(fn($query, $state) => $state ? $query->whereYear('tanggal', $state) : null),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('detail')
                ->label('Detail')
                ->icon('heroicon-o-eye')
                ->url(fn($record) => RekapPembelianDetail::getUrl(['tanggal' => $record->tanggal])),
        ];
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'Silahkan pilih bulan dan tahun untuk melihat rekap pembelian';
    }
} 