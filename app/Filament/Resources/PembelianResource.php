<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PembelianResource\Pages;
use App\Filament\Resources\PembelianResource\RelationManagers;
use App\Models\barang;
use App\Models\Pembelian;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
//use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
//use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;

class PembelianResource extends Resource
{
    protected static ?string $model = Pembelian::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'Pembelian Gas';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form

    {
        return $form
            ->schema([
                 DatePicker::make('tanggal')
                ->label('Tanggal')
                ->required(),

                Select::make('id_barang')
                    ->label('Barang')
                    ->relationship('barang', 'nama_barang')
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated( function ($state, Set $set, Get $get) {
                        $harga = barang::find($state)?->harga_beli ?? 0;
                        $set('harga_beli', $harga );
                        // hitung subtotal
                        $qty = $get('qty') ?? 0;
                        $set('subtotal', $qty * $harga);
                    }),


                    TextInput::make('qty')
                    ->label('Jumlah (Qty)')
                    ->numeric()
                    ->minValue(1)
                    ->required()
                    ->reactive()
                    ->afterStateUpdated( function ($state, Set $set, Get $get) {
                        $harga = $get('harga_beli') ?? 0;
                        $set('subtotal', $state * $harga);
                    }),

                TextInput::make('harga_beli')
                    ->label('Harga Beli')
                    ->numeric()
                    ->readOnly()    
                    ->required(),

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
            ->query(fn() => static::getEloquentQuery())
            ->columns([
                TextColumn::make('tanggal')
                ->date('d M Y')
                ->sortable(),
                
                TextColumn::make('barang.nama_barang')
                    ->label('Barang'),

                TextColumn::make('barang.harga_beli')
                    ->label('Harga Beli')
                    ->money('IDR', true),

                TextColumn::make('qty')
                    ->label('Qty'),

                TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->money('IDR', true)
            ])
            ->filters([
                //TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListPembelians::route('/'),
            'create' => Pages\CreatePembelian::route('/create'),
            'edit' => Pages\EditPembelian::route('/{record}/edit'),
        ];
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $barang = barang::find($data['id_barang']);
        $data['harga_beli'] = $barang->harga_beli;
        $data['subtotal'] = $barang->harga_beli * $data['qty'];
        $data['harga'] = $barang->harga_beli;

        // Tambah stok barang
        $barang->sisa_stok += $data['qty'];
        $barang->save();

        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        $barang = barang::find($data['id_barang']);
        $data['harga_beli'] = $barang->harga_beli;
        $data['subtotal'] = $barang->harga_beli * $data['qty'];
        $data['harga'] = $barang->harga_beli;

        return $data;
    }
}
