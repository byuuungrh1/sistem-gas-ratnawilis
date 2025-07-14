<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use app\Models\Pelanggan;

class ResetPembelianBulanan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-pembelian-bulanan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Pelanggan::query()->update([
            'jumlah_pembelian_bulan_ini' => 0,
            'tanggal_terakhir_beli' => null,
        ]);

        $this->info('Jumlah pembelian pelanggan direset.');
        }
}
