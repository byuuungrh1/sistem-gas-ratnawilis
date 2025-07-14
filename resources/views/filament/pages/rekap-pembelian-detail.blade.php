{{-- Menghitung total subtotal dan total qty dari koleksi pembelians --}}
@php
    use Illuminate\Support\Carbon;
    $total = $this->pembelians->sum('subtotal');
    $totalQty = $this->pembelians->sum('qty');
@endphp

<x-filament::page>
    {{-- CSS khusus untuk mode cetak --}}
    <style>
        @media print {
            body * { visibility: hidden !important; }
            #print-area, #print-area * { visibility: visible !important; }
            #print-area { position: absolute; left: 0; top: 0; width: 100%; }

            .no-print { display: none !important; }
        }
    </style>

    <div id="print-area">
        {{-- Judul laporan menampilkan tanggal yang dipilih --}}
        <h2 class="text-xl font-bold mb-4">Rekap Pembelian Tanggal {{ Carbon::parse($this->tanggal)->format('d M Y') }}</h2>

        {{-- Tombol cetak, tidak ditampilkan saat print --}}
        <div class="mb-4 no-print">
            <a href="#" class="filament-button filament-button--primary" onclick="window.print()">Print PDF</a>
        </div>

        {{-- Tabel detail pembelian --}}
        <div class="overflow-x-auto">
            <table class="filament-tables-table w-full text-center">
                <thead>
                    <tr>
                        <th class="px-4 py-2 text-center">No</th>
                        <th class="px-4 py-2 text-center">Barang</th>
                        <th class="px-4 py-2 text-center">Qty</th>
                        <th class="px-4 py-2 text-center">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($this->pembelians as $pembelian)
                        <tr>
                            <td class="px-4 py-2 text-center">{{ $loop->iteration }}</td>
                            <td class="px-4 py-2 text-center">{{ $pembelian->barang->nama_barang ?? '-' }}</td>
                            <td class="px-4 py-2 text-center">{{ $pembelian->qty }}</td>
                            <td class="px-4 py-2 text-center">Rp {{ number_format($pembelian->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    {{-- Baris total keseluruhan --}}
                    <tr class="font-bold text-center">
                        <td colspan="2" class="text-center px-4 py-2">Total</td>
                        <td class="px-4 py-2 text-center">{{ $totalQty }}</td>
                        <td class="px-4 py-2 text-center">Rp {{ number_format($total, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</x-filament::page> 