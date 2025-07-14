<?php

namespace App\Filament\Resources\PenjualanResource\Pages;

use App\Filament\Resources\PenjualanResource;
use App\Models\Barang;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Validation\ValidationException;
use Filament\Notifications\Notification;

class CreatePenjualanGas extends CreateRecord
{
    protected static string $resource = PenjualanResource::class;

    public function mount(): void
    {
        parent::mount();
        $this->prefillGasLpg3kg();
    }

    /**
     * Prefill form with Gas LPG 3KG as barang.
     */
    protected function prefillGasLpg3kg(): void
    {
        $gasBarang = Barang::where('nama_barang', 'GAS LPG 3KG')->first();
        if ($gasBarang) {
            $this->form->fill([
                'tanggal'    => now()->format('Y-m-d'),
                'id_barang'  => $gasBarang->id,
                'harga_jual' => $gasBarang->harga_jual,
                'qty'        => 1,
                'subtotal'   => $gasBarang->harga_jual,
            ]);
        }
    }

    protected function getFormSchema(): array
    {
        $gasBarang = Barang::where('nama_barang', 'GAS LPG 3KG')->first();

        return [
            DatePicker::make('tanggal')
                ->label('Tanggal')
                ->default(now())
                ->required(),

            Select::make('pelanggan_id')
                ->label('Pelanggan')
                ->relationship('pelanggan', 'nama_lengkap')
                ->searchable()
                ->required(),

            Hidden::make('id_barang')
                ->default($gasBarang?->id),

            Placeholder::make('barang')
                ->label('Barang')
                ->content($gasBarang?->nama_barang ?? 'GAS LPG 3KG'),

            TextInput::make('qty')
                ->label('Jumlah (Qty)')
                ->numeric()
                ->minValue(1)
                ->default(1)
                ->required()
                ->afterStateUpdated(function ($state, Set $set, Get $get) {
                    $harga = $get('harga_jual') ?? 0;
                    $set('subtotal', $state * $harga);
                }),

            TextInput::make('harga_jual')
                ->label('Harga Jual')
                ->numeric()
                ->default($gasBarang?->harga_jual)
                ->readOnly()
                ->required(),

            TextInput::make('subtotal')
                ->label('Subtotal')
                ->numeric()
                ->default($gasBarang?->harga_jual)
                ->readOnly()
                ->required(),
        ];
    }

    /**
     * Keep previous form state when user clicks "Buat & buat lainnya".
     */
    public function createAnother(): void
    {
        $state = $this->form->getState();

        parent::createAnother(); // This resets the form.

        // Re-apply the previous state so fields stay filled.
        $this->form->fill($state);
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        try {
            return parent::handleRecordCreation($data);
        } catch (ValidationException $e) {
            $messages = collect($e->errors())->flatten()->implode(' ');
            Notification::make()
                ->title('Penjualan Gagal')
                ->body($messages ?: ($e->getMessage() ?: 'Penjualan gagal diproses.'))
                ->danger()
                ->send();
            throw $e;
        }
    }
}
