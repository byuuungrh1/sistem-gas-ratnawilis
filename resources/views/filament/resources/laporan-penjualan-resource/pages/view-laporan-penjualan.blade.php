@php
    use Illuminate\Support\Carbon;
    $total = $penjualans->sum('subtotal');
@endphp

<x-filament::page>
    <h2 class="text-xl font-bold mb-4">Rekap Penjualan Tanggal {{ Carbon::parse($tanggal)->format('d M Y') }}</h2>

    <div class="mb-4">
        <a href="#" class="filament-button filament-button--primary" onclick="window.print()">
            Print PDF
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="filament-tables-table w-full">
            <thead>
                <tr>
                    <th class="px-4 py-2">Pelanggan</th>
                    <th class="px-4 py-2">Barang</th>
                    <th class="px-4 py-2">Qty</th>
                    <th class="px-4 py-2">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($penjualans as $penjualan)
                    <tr>
                        <td class="px-4 py-2">{{ $penjualan->pelanggan->nama_lengkap ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $penjualan->barang->nama_barang ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $penjualan->qty }}</td>
                        <td class="px-4 py-2">Rp {{ number_format($penjualan->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="font-bold">
                    <td colspan="3" class="text-right px-4 py-2">Total</td>
                    <td class="px-4 py-2">Rp {{ number_format($total, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</x-filament::page> 