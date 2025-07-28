<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('pemeriksaan.index') }}"
            class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-100">
                @svg('heroicon-o-arrow-left', 'h-4 w-4 mr-2 text-gray-600')
                Kembali
            </a>
            <h2 class="text-2xl font-bold text-gray-800">🩺 Detail Pemeriksaan</h2>
            @if ($pemeriksaan->sudah_dilayani)
                <span class="text-sm font-medium text-white bg-green-600 px-2 py-1 rounded-full">Sudah Dilayani</span>
            @else
                <span class="text-sm font-medium text-white bg-yellow-500 px-2 py-1 rounded-full">Belum Dilayani</span>
            @endif
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

            {{-- Resep Obat --}}
            <x-card>
                <x-slot name="title">💊 Resep Obat</x-slot>
                @if ($pemeriksaan->reseps->count())
                    <ul class="list-disc ml-6 space-y-1 text-gray-700">
                        @foreach ($pemeriksaan->reseps as $resep)
                            <li>
                                <strong>{{ $resep->medicine_name }}</strong> - {{ $resep->quantity }} pcs
                                <span class="text-sm text-gray-500">(Rp {{ number_format($resep->prices) }})</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-500 italic">Tidak ada resep.</p>
                @endif
            </x-card>

            {{-- Berkas Pemeriksaan --}}
            <x-card>
                <x-slot name="title">📂 Berkas Pemeriksaan</x-slot>
                @forelse ($pemeriksaan->berkas as $file)
                    <div>
                        <a href="{{ Storage::url($file->file_path) }}" target="_blank" class="text-blue-600 hover:underline">
                            📎 Lihat Berkas {{ $loop->iteration }}
                        </a>
                    </div>
                @empty
                    <p class="text-gray-500 italic">Tidak ada berkas.</p>
                @endforelse
            </x-card>

            {{-- Tombol Edit --}}
            @if ($pemeriksaan->sudah_dilayani === 0)
                <div class="text-right">
                    <a href="{{ route('pemeriksaan.edit', $pemeriksaan) }}"
                    class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white text-sm font-semibold rounded hover:bg-yellow-600 transition">
                        ✏️ Edit Pemeriksaan
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
