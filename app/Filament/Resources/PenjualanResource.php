<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenjualanResource\Pages;
use App\Filament\Resources\PenjualanResource\RelationManagers;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use App\Models\Barang;
use App\Models\penjualan;
use App\Models\pelanggan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;


class PenjualanResource extends Resource
{
    protected static ?string $model = penjualan::class;

    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    protected static ?string $navigationLabel = 'Penjualan Gas';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                 DatePicker::make('tanggal')
                ->label('Tanggal')
                ->default(Carbon::now())
                ->required(),

                Select::make('nik')
                ->label('NIK Pelanggan')
                ->options(Pelanggan::all()->pluck('nik', 'nik')) // key dan value = nik
                ->searchable()
                ->preload()
                ->live()
                ->required()
                ->afterStateUpdated(function ($state, Set $set) {
                    $pelanggan = Pelanggan::where('nik', $state)->first();
                    if ($pelanggan) {
                        $set('nama_lengkap', $pelanggan->nama_lengkap);
                        $set('pelanggan_id', $pelanggan->id); // ⬅️ INI YANG WAJIB DITAMBAH
                    } else {
                        $set('nama_lengkap', null);
                        $set('pelanggan_id', null);
                    }
                }),
           
                TextInput::make('nama_lengkap')
                ->label('Nama Pelanggan')
                ->readOnly()
                ->required(),

                Hidden::make('pelanggan_id') // ⬅️ field tersembunyi untuk menyimpan ID
                ->required(),

                Select::make('id_barang')
                    ->label('Barang')
                    ->relationship('barang', 'nama_barang')
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated( function ($state, Set $set, Get $get) {
                        $harga = Barang::find($state)?->harga_jual ?? 0;
                        $set('harga_jual', $harga );
                        // hitung subtotal
                        $qty = $get('qty') ?? 0;
                        $set('subtotal', $harga * $qty );
                    }),

                TextInput::make('qty')
                    ->label('Jumlah (Qty)')
                    ->numeric()
                    ->minValue(1)
                    ->required()
                    ->reactive()
                    ->afterStateUpdated( function ($state, Set $set, Get $get) {
                        $harga = $get('harga_jual') ?? 0;
                        $set('subtotal', $state * $harga);
                    }),

                // harga jual
                TextInput::make('harga_jual')
                    ->label('Harga Jual')
                    ->numeric()
                    ->required()
                    ->readOnly()
                    ->reactive()
                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                        $qty = $get('qty') ?? 0;
                        $set('subtotal', $state * $qty);
                    }),

                // subtotal
                TextInput::make('subtotal')
                    ->label('Subtotal')
                    ->numeric()
                    ->readOnly()
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal')
                ->date('d M Y')
                ->sortable(),

                TextColumn::make('pelanggan.nik')
                    ->label('NIK')
                    ->sortable(),

                TextColumn::make('pelanggan.nama_lengkap')
                    ->label('Pelanggan'),

                TextColumn::make('barang.nama_barang')
                    ->label('Barang'),

                TextColumn::make('barang.harga_jual')
                    ->label('Harga Jual')
                    ->money('IDR', true),

                TextColumn::make('qty')
                    ->label('Qty'),

                TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->money('IDR', true), 
                    

                ])
                ->filters([
                    //
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

    // public static function beforeCreate($record)
    // {
    //     $pelanggan = Pelanggan::find($record->pelanggan_id);

    //     if (!$pelanggan) {
    //         Notification::make()
    //             ->title('Pelanggan tidak ditemukan.')
    //             ->danger()
    //             ->send();

    //         return false;
    //     }

    //     $batas = $pelanggan->status_pengambilan_gas === 'umkm' ? 8 : 4;

    //     $jumlahPembelianBulanIni = Penjualan::where('pelanggan_id', $pelanggan->id)
    //         ->whereMonth('tanggal', now()->month)
    //         ->whereYear('tanggal', now()->year)
    //         ->count();

    //     if ($jumlahPembelianBulanIni >= $batas) {
    //         Notification::make()
    //             ->title("Batas pembelian gas untuk pelanggan ini telah tercapai.")
    //             ->body("Status: {$pelanggan->status_pengambilan_gas} | Maksimal: {$batas} kali/bulan.")
    //             ->danger()
    //             ->send();

    //         return false;
    //     }
    // }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $pelanggan = Pelanggan::find($data['pelanggan_id']);

        $kuota = $pelanggan->status_pengambilan_gas === 'umkm' ? 8 : 4;

        // Hitung jumlah pembelian bulan ini dari tabel penjualan
        $jumlahPembelianBulanIni = penjualan::where('pelanggan_id', $pelanggan->id)
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->count();

        if ($jumlahPembelianBulanIni > $kuota) {
            throw ValidationException::withMessages([
                'pelanggan_id' => "Pelanggan ini telah melebihi kuota bulanan ({$kuota} kali).",
            ]);
        }

        // Hitung subtotal
        $barang = Barang::find($data['id_barang']);
        $data['harga_jual'] = $barang->harga_jual;
        $data['subtotal'] = $barang->harga_jual * $data['qty'];
        $data['harga'] = $barang->harga_jual;

        // Kurangi stok
        $barang->sisa_stok -= $data['qty'];
        $barang->save();

       return $data;
    }
    public static function mutateFormDataBeforeUpdate(array $data): array
    {
        $barang = Barang::find($data['id_barang']);
        $data['harga_jual'] = $barang->harga_jual;
        $data['subtotal'] = $barang->harga_jual * $data['qty'];
        $data['harga'] = $barang->harga_jual;
        return $data;
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
            'index' => Pages\ListPenjualans::route('/'),
            'create' => Pages\CreatePenjualan::route('/create'),
            'create-gas' => Pages\CreatePenjualanGas::route('/create-gas'),
            'edit' => Pages\EditPenjualan::route('/{record}/edit'),
        ];
    }
}
