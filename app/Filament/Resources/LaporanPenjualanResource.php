<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporanPenjualanResource\Pages;
use App\Filament\Resources\LaporanPenjualanResource\RelationManagers;
use App\Models\LaporanPenjualan;
use App\Models\penjualan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\DB;
use Filament\Pages\Actions\ViewAction;
use Filament\Pages\Actions\Action;
use Filament\Pages\Actions\DeleteAction;
use Filament\Pages\Actions\EditAction;
use Filament\Pages\Actions\RestoreAction;
use Filament\Pages\Actions\ForceDeleteAction;
use Filament\Resources\Pages\ViewRecord;


class LaporanPenjualanResource extends Resource
{
    protected static ?string $model = LaporanPenjualan::class;
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-up';

    protected static ?string $navigationLabel = 'Laporan Penjualan';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        // Query: group by tanggal, sum subtotal
        $query = Penjualan::query()
            ->select([
                DB::raw('MIN(id) as id'),
                'tanggal',
                DB::raw('SUM(subtotal) as total_subtotal'),
            ])
            ->groupBy('tanggal');

        return $table
            ->query($query)
            ->columns([
                TextColumn::make('tanggal')->date('d M Y')->label('Tanggal')->sortable(),
                TextColumn::make('total_subtotal')->label('Total Penjualan')->money('IDR', true),
            ])
            ->filters([
                // Filter bulan
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
                    ->query(function ($query, $state) {
                        if ($state) {
                            $query->whereMonth('tanggal', $state);
                        }
                    }),
                // Filter tahun
                SelectFilter::make('tahun')
                    ->label('Tahun')
                    ->options(function () {
                        $years = Penjualan::selectRaw('YEAR(tanggal) as year')->distinct()->pluck('year')->toArray();
                        return array_combine($years, $years);
                    })
                    ->query(function ($query, $state) {
                        if ($state) {
                            $query->whereYear('tanggal', $state);
                        }
                    }),
            ])
            ->actions([
                // Tidak ada aksi detail di resource; gunakan Page RekapPenjualan
            ])
            ->bulkActions([
                // Tidak ada bulk action
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaporanPenjualans::route('/'),
            // Tidak ada create/edit/view, data rekap otomatis
        ];
    }
}
