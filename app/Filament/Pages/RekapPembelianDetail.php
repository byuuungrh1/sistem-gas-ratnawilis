<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\pembelian;

class RekapPembelianDetail extends Page
{
    protected static string $view = 'filament.pages.rekap-pembelian-detail';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'rekap-pembelian/{tanggal}';

    public string $tanggal;
    public $pembelians;

    public function mount(string $tanggal): void
    {
        $this->tanggal = $tanggal;
        $this->pembelians = pembelian::with('barang')->whereDate('tanggal', $tanggal)->get();
    }
} 