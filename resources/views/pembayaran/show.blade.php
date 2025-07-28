<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('pembayaran.index') }}"
            class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-100">
                @svg('heroicon-o-arrow-left', 'h-4 w-4 mr-2 text-gray-600')
                Kembali
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Pembayaran Resep</h2>
        </div>
    </x-slot>

    <div class="py-2">
        <div class="py-6 max-w-5xl mx-auto space-y-6">
            {{-- Data Pasien --}}
            <x-card>
                <x-slot name="title">👤 Data Pasien</x-slot>
                <div class="space-y-1 text-gray-700">
                    <p><strong>Nama:</strong> {{ $pemeriksaan->nama_pasien }}</p>
                    <p><strong>Waktu Pemeriksaan:</strong> {{ \Carbon\Carbon::parse($pemeriksaan->waktu_pemeriksaan)->translatedFormat('d F Y') }}</p>
                </div>
            </x-card>

            {{-- Tanda Vital --}}
            <x-card>
                <x-slot name="title">🧪 Tanda Vital</x-slot>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 text-gray-700">
                    <div><strong>Tinggi:</strong> {{ $pemeriksaan->tinggi_badan }} cm</div>
                    <div><strong>Berat:</strong> {{ $pemeriksaan->berat_badan }} kg</div>
                    <div><strong>Systole:</strong> {{ $pemeriksaan->systole }}</div>
                    <div><strong>Diastole:</strong> {{ $pemeriksaan->diastole }}</div>
                    <div><strong>Heart Rate:</strong> {{ $pemeriksaan->heart_rate }}</div>
                    <div><strong>Respiration:</strong> {{ $pemeriksaan->respiration_rate }}</div>
                    <div><strong>Suhu:</strong> {{ $pemeriksaan->suhu_tubuh }} °C</div>
                </div>
            </x-card>

            {{-- Catatan Dokter --}}
            <x-card>
                <x-slot name="title">📝 Catatan Dokter</x-slot>
                <p class="text-gray-700">{{ $pemeriksaan->catatan }}</p>
            </x-card>

            <x-card>
                <x-slot name="title">💊 Resep Obat</x-slot>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="text-left px-2 py-1">Obat</th>
                            <th class="text-left px-2 py-1">Jumlah</th>
                            <th class="text-left px-2 py-1">Harga Satuan</th>
                            <th class="text-left px-2 py-1">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $grandTotal = 0;
                        @endphp
                        @foreach ($pemeriksaan->reseps as $item)
                            @php
                                $subtotal = $item->quantity * $item->prices;
                                $grandTotal += $subtotal;
                            @endphp
                            <tr class="border-b">
                                <td class="px-2 py-1">{{ $item->medicine_name }}</td>
                                <td class="px-2 py-1">{{ $item->quantity }}</td>
                                <td class="px-2 py-1">Rp {{ number_format($item->prices) }}</td>
                                <td class="px-2 py-1">Rp {{ number_format($subtotal) }}</td>
                            </tr>
                        @endforeach
                        <tr class="bg-gray-50 font-semibold">
                            <td colspan="3" class="px-2 py-2 text-right">Total</td>
                            <td class="px-2 py-2">Rp {{ number_format($grandTotal) }}</td>
                        </tr>
                    </tbody>
                </table>
            </x-card>

            <div class="flex justify-end">
                <form method="POST" action="{{ route('pembayaran.selesai', $pemeriksaan) }}"
                    onsubmit="return confirm('Apakah Anda yakin ingin menyelesaikan pembayaran ini?')">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        Proses Pembayaran
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
