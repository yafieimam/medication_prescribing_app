<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Daftar Pembayaran Resep</h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto">
        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif

        <div class="bg-white shadow-md rounded p-4 overflow-x-auto">
            <table class="w-full text-sm text-left table-auto">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Pasien</th>
                        <th class="px-4 py-2">Tanggal</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Jumlah Obat</th>
                        <th class="px-4 py-2">Total Harga</th>
                        <th class="px-4 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pemeriksaan as $item)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $item->nama_pasien }}</td>
                            <td class="px-4 py-2">{{ \Carbon\Carbon::parse($item->waktu_pemeriksaan)->format('d M Y') }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 rounded text-white text-xs
                                    {{ $item->sudah_dilayani === 0 ? 'bg-yellow-400' : 'bg-green-600' }}">
                                    {{ $item->sudah_dilayani === 0 ? 'Belum Dilayani' : 'Sudah Dilayani' }}
                                </span>
                            </td>
                            <td class="px-4 py-2">{{ $item->reseps->sum('quantity') }} item</td>
                            <td class="px-4 py-2">Rp {{ number_format($item->reseps->sum(fn($i) => $i->quantity * $i->prices)) }}</td>
                            <td class="px-4 py-2 space-x-2">
                                @if ($item->sudah_dilayani === 0)
                                    <a href="{{ route('pembayaran.show', $item) }}"
                                    class="px-3 py-1 bg-yellow-500 text-white text-sm rounded hover:bg-yellow-600">
                                        Selesaikan
                                    </a>
                                @else
                                    <a href="{{ route('pembayaran.cetak', $item) }}"
                                    class="px-3 py-1 bg-indigo-500 text-white text-sm rounded hover:bg-indigo-600">
                                        Cetak PDF
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-4 text-gray-500">Tidak ada resep menunggu pembayaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
