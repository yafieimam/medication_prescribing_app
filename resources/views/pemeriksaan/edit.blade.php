<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Pemeriksaan
        </h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto">
        <form method="POST" action="{{ route('dokter.pemeriksaan.update', $pemeriksaan) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <x-input-label value="Nama Pasien" for="nama_pasien" />
                <x-text-input name="nama_pasien" id="nama_pasien" value="{{ old('nama_pasien', $pemeriksaan->nama_pasien) }}" class="w-full" required />
            </div>

            <div class="mb-4">
                <x-input-label value="Waktu Pemeriksaan" for="waktu_pemeriksaan" />
                <x-text-input type="date" name="waktu_pemeriksaan" id="waktu_pemeriksaan" value="{{ old('waktu_pemeriksaan', $pemeriksaan->waktu_pemeriksaan) }}" class="w-full" required />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach (['tinggi_badan','berat_badan','systole','diastole','heart_rate','respiration_rate','suhu_tubuh'] as $field)
                    <x-input-group label="{{ ucwords(str_replace('_', ' ', $field)) }}" name="{{ $field }}" :value="$pemeriksaan->$field" />
                @endforeach
            </div>

            <div class="mt-4">
                <x-input-label for="catatan" value="Catatan Dokter" />
                <textarea name="catatan" id="catatan" rows="4" class="w-full border rounded">{{ old('catatan', $pemeriksaan->catatan) }}</textarea>
            </div>

            {{-- ✅ Resep Obat --}}
            <h3 class="text-lg font-semibold mt-6 mb-2">Resep Obat</h3>
            <div id="resep-container" class="space-y-4">
                @foreach ($pemeriksaan->resep as $i => $resep)
                    <div class="resep-row flex items-center gap-4">
                        <select name="resep[{{ $i }}][medicine_id]" class="medicine-select w-full" required data-initial-id="{{ $resep->medicine_id }}" data-initial-name="{{ $resep->nama_obat }}"></select>
                        <input type="number" name="resep[{{ $i }}][jumlah]" class="border rounded p-2 w-32" placeholder="Jumlah" value="{{ $resep->jumlah }}" required>
                        <button type="button" class="btn-delete-resep text-red-500">Hapus</button>
                    </div>
                @endforeach
            </div>
            <button type="button" id="add-resep" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded">Tambah Resep</button>

            {{-- ✅ File Upload --}}
            <h3 class="text-lg font-semibold mt-6 mb-2">Berkas Pemeriksaan</h3>
            <div class="mb-4">
                <p>Berkas saat ini:</p>
                @foreach ($pemeriksaan->files as $file)
                    <div class="flex items-center gap-2">
                        <a href="{{ Storage::url($file->path) }}" target="_blank" class="text-blue-500 underline">Lihat File {{ $loop->iteration }}</a>
                    </div>
                @endforeach
            </div>
            <div class="mb-4">
                <x-input-label value="Upload Berkas Baru (boleh lebih dari satu)" />
                <input type="file" name="files[]" multiple class="block w-full" />
            </div>

            <x-primary-button>Simpan Perubahan</x-primary-button>
        </form>
    </div>

    {{-- Script Select2 --}}
    @push('scripts')
    <script>
        let resepIndex = {{ $pemeriksaan->resep->count() }};

        function initSelect2(select) {
            $(select).select2({
                placeholder: "Cari nama obat...",
                ajax: {
                    url: '{{ route('ajax.obat.autocomplete') }}',
                    dataType: 'json',
                    delay: 250,
                    data: params => ({ q: params.term }),
                    processResults: data => ({ results: data.results })
                }
            });

            // Set nilai awal dari data-attribute (untuk existing data)
            const initId = $(select).data('initial-id');
            const initName = $(select).data('initial-name');

            if (initId && initName) {
                const option = new Option(initName, initId, true, true);
                $(select).append(option).trigger('change');
            }
        }

        $(document).ready(function () {
            $('.medicine-select').each(function () {
                initSelect2(this);
            });

            $('#add-resep').on('click', function () {
                const newRow = `
                    <div class="resep-row flex items-center gap-4">
                        <select name="resep[${resepIndex}][medicine_id]" class="medicine-select w-full" required></select>
                        <input type="number" name="resep[${resepIndex}][jumlah]" class="border rounded p-2 w-32" placeholder="Jumlah" required>
                        <button type="button" class="btn-delete-resep text-red-500">Hapus</button>
                    </div>
                `;
                $('#resep-container').append(newRow);
                initSelect2(`#resep-container .resep-row:last-child .medicine-select`);
                resepIndex++;
            });

            $('#resep-container').on('click', '.btn-delete-resep', function () {
                $(this).closest('.resep-row').remove();
            });
        });
    </script>
    @endpush
</x-app-layout>
