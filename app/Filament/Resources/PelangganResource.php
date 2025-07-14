<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PelangganResource\Pages;
use App\Filament\Resources\PelangganResource\RelationManagers;
use App\Models\Pelanggan;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use App\Models\penjualan;
use Filament\Tables\Filters\Filter;

class PelangganResource extends Resource
{
    protected static ?string $model = Pelanggan::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationLabel = 'Pelanggan';
    protected static ?string $navigationGroup = 'Data';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama_lengkap')
                    ->label('Nama Lengkap')
                    ->required()
                    ->columnSpanFull(),

                TextInput::make('nik')
                    ->label('NIK')
                    ->required()
                    ->unique(ignoreRecord: true) 
                    ->maxLength(16),

                TextInput::make('no_telepon')
                    ->label('No. Telepon')
                    ->tel()
                    ->required(),

                TextInput::make('alamat')
                    ->label('Alamat')
                    ->required(),

                Select::make('status_pengambilan_gas')
                    ->label('Status Pengambilan Gas')
                    ->required()
                    ->options([
                        'rumah_tangga' => 'Rumah Tangga',
                        'umkm' => 'UMKM',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $currentYear = now()->year;
        $years = [];
        for ($i = $currentYear; $i >= $currentYear - 5; $i--) {
            $years[$i] = $i;
        }
        return $table
            ->columns([
                TextColumn::make('nama_lengkap')
                    ->label('Nama Lengkap')
                    ->searchable(),
                TextColumn::make('nik')
                    ->label('NIK')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('NIK berhasil disalin')
                    ->copyMessageDuration(1500),
                TextColumn::make('alamat')->label('Alamat'),
                TextColumn::make('no_telepon')->label('No. Telepon'),
                TextColumn::make('status_pengambilan_gas')->label('Status Pengambilan Gas'),
                TextColumn::make('<jumlah_pembelia></jumlah_pembelia>n_bulan_ini')->label('Jumlah Pembelian Bulan Ini')
                ->getStateUsing(function ($record, $livewire) {
                    // Ambil filters dari berbagai kemungkinan properti
                    $filters = method_exists($livewire, 'getFilters') ? $livewire->getFilters() : (
                        property_exists($livewire, 'filters') ? $livewire->filters : (
                            property_exists($livewire, 'tableFilters') ? $livewire->tableFilters : []
                        )
                    );

                    // Helper untuk mengambil nilai pertama yang tersedia dari beberapa path
                    $resolve = function(array $paths, $default = null) use ($filters) {
                        foreach ($paths as $path) {
                            $value = data_get($filters, $path);
                            if (! is_null($value) && $value !== '') {
                                return $value;
                            }
                        }
                        return $default;
                    };

                    $month = $resolve([
                        'bulan.bulan',          // ['bulan' => ['bulan' => 6]]
                        'bulan.value.bulan',    // ['bulan' => ['value' => ['bulan' => 6]]]
                        'bulan.value',          // ['bulan' => ['value' => 6]]
                        'bulan',                // ['bulan' => 6]
                    ], now()->month);

                    $year = $resolve([
                        'tahun.tahun',
                        'tahun.value.tahun',
                        'tahun.value',
                        'tahun',
                    ], now()->year);

                    return penjualan::where('pelanggan_id', $record->id)
                        ->whereYear('tanggal', $year)
                        ->whereMonth('tanggal', $month)
                        ->sum('qty');
                }),
            ])
            ->filters([
                Filter::make('bulan')
                    ->form([
                        Select::make('bulan')
                            ->label('Bulan')
                            ->options($months)
                            ->default(now()->month),
                    ]),
                Filter::make('tahun')
                    ->form([
                        Select::make('tahun')
                            ->label('Tahun')
                            ->options($years)
                            ->default($currentYear),
                    ]),
            ])            
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListPelanggans::route('/'),
            'create' => Pages\CreatePelanggan::route('/create'),
            'edit' => Pages\EditPelanggan::route('/{record}/edit'),
        ];
    }
}
