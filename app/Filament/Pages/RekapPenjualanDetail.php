<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\penjualan;

class RekapPenjualanDetail extends Page
{
    protected static string $view = 'filament.pages.rekap-penjualan-detail';

    protected static bool $shouldRegisterNavigation = false; // hide from navigation

    protected static ?string $slug = 'rekap-penjualan/{tanggal}';

    public string $tanggal;
    public $penjualans;

    public function mount(string $tanggal): void
    {
        $this->tanggal = $tanggal;
        $this->penjualans = Penjualan::with(['pelanggan', 'barang'])->whereDate('tanggal', $tanggal)->get();
    }
} 