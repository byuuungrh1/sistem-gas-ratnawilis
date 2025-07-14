<?php

namespace App\Filament\Resources\PenjualanResource\Pages;

use App\Filament\Resources\PenjualanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

class CreatePenjualan extends CreateRecord
{
    protected static string $resource = PenjualanResource::class;

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
