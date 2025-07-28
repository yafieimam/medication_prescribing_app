<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Daftar Pemeriksaan
            </h2>
            <a href="{{ route('pemeriksaan.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">
                + Pemeriksaan Baru
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto">
        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow-md rounded p-4 overflow-x-auto">
            <table class="w-full text-sm text-left table-auto">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2">No</th>
                        <th class="px-4 py-2">Nama Pasien</th>
                        <th class="px-4 py-2">Waktu</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Resep</th>
                        <th class="px-4 py-2">Berkas</th>
                        <th class="px-4 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pemeriksaan as $item)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $loop->iteration + ($pemeriksaan->currentPage() - 1) * $pemeriksaan->perPage() }}</td>
                            <td class="px-4 py-2">{{ $item->nama_pasien }}</td>
                            <td class="px-4 py-2">{{ $item->waktu_pemeriksaan }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 rounded text-white text-xs
                                    {{ $item->sudah_dilayani === 0 ? 'bg-yellow-400' : 'bg-green-600' }}">
                                    {{ $item->sudah_dilayani === 0 ? 'Belum Dilayani' : 'Sudah Dilayani' }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-center">{{ $item->reseps_count }}</td>
                            <td class="px-4 py-2 text-center">{{ $item->berkas_count }}</td>
                            <td class="px-4 py-2">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('pemeriksaan.show', $item) }}" title="Lihat"
                                    class="inline-flex items-center justify-center rounded-md p-1 hover:bg-blue-100">
                                        @svg('heroicon-o-eye', 'h-5 w-5 text-blue-500')
                                    </a>
                                    @if ($item->sudah_dilayani === 0)
                                        <a href="{{ route('pemeriksaan.edit', $item) }}" title="Edit"
                                        class="inline-flex items-center justify-center rounded-md p-1 hover:bg-yellow-100">
                                            @svg('heroicon-o-pencil', 'h-5 w-5 text-yellow-400')
                                        </a>
                                    @endif
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">Belum ada data pemeriksaan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-4">
                {{ $pemeriksaan->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
