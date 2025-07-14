<?php

namespace App\Filament\Resources\PenjualanResource\Pages;

use App\Filament\Resources\PenjualanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPenjualans extends ListRecords
{
    protected static string $resource = PenjualanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('createGas')
                ->label('Penjualan Gas LPG 3KG')
                ->icon('heroicon-o-plus')
                ->url(PenjualanResource::getUrl('create-gas')),
        ];
    }
}
