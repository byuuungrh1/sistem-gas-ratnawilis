{{-- Menghitung total subtotal dan total qty dari koleksi penjualans --}}
@php
    use Illuminate\Support\Carbon;
    $total = $this->penjualans->sum('subtotal');
    $totalQty = $this->penjualans->sum('qty');
@endphp

<x-filament::page>
    {{-- Style khusus print --}}
    <style>
        @media print {
            /* Sembunyikan seluruh halaman terlebih dahulu */
            body * {
                visibility: hidden !important;
            }

            /* Tampilkan hanya area yang ingin dicetak */
            #print-area, #print-area * {
                visibility: visible !important;
            }

            /* Posisi di pojok kiri-atas agar memenuhi halaman */
            #print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 0;
            }

            /* Sembunyikan elemen dengan class no-print saat cetak */
            .no-print {
                display: none !important;
            }
        }
    </style>

    <div id="print-area">
        {{-- Judul laporan menampilkan tanggal yang dipilih --}}
        <h2 class="text-xl font-bold mb-4">Rekap Penjualan Tanggal {{ Carbon::parse($this->tanggal)->format('d M Y') }}</h2>

        {{-- Tombol cetak, tidak ditampilkan saat print berkat class no-print --}}
        <div class="mb-4 no-print">
            <a href="#" class="filament-button filament-button--primary" onclick="window.print()">
                Print PDF
            </a>
        </div>

        {{-- Tabel detail penjualan --}}
        <div class="overflow-x-auto">
            <table class="filament-tables-table w-full text-center">
                <thead>
                    <tr>
                        <th class="px-4 py-2 text-center">No</th>
                        <th class="px-4 py-2 text-center">NIK</th>
                        <th class="px-4 py-2 text-center">Pelanggan</th>
                        <th class="px-4 py-2 text-center">Alamat</th>
                        <th class="px-4 py-2 text-center">Barang</th>
                        <th class="px-4 py-2 text-center">Qty</th>
                        <th class="px-4 py-2 text-center">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($this->penjualans as $penjualan)
                        <tr>
                            <td class="px-4 py-2 text-center">{{ $loop->iteration }}</td>
                            <td class="px-4 py-2 text-center">{{ $penjualan->pelanggan->nik ?? '-' }}</td>
                            <td class="px-4 py-2 text-center">{{ $penjualan->pelanggan->nama_lengkap ?? '-' }}</td>
                            <td class="px-4 py-2 text-center">{{ $penjualan->pelanggan->alamat ?? '-' }}</td>
                            <td class="px-4 py-2 text-center">{{ $penjualan->barang->nama_barang ?? '-' }}</td>
                            <td class="px-4 py-2 text-center">{{ $penjualan->qty }}</td>
                            <td class="px-4 py-2 text-center">Rp {{ number_format($penjualan->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    {{-- Baris total keseluruhan --}}
                    <tr class="font-bold text-center">
                        <td colspan="4"></td>
                        <td class="px-4 py-2 text-center">Total</td>
                        <td class="px-4 py-2 text-center">{{ $totalQty }}</td>
                        <td class="px-4 py-2 text-center">Rp {{ number_format($total, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div> <!-- /print-area -->
</x-filament::page> 