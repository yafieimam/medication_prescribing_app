<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-6">
            {{-- Tombol Kembali --}}
            <a href="{{ route('pemeriksaan.index') }}"
            class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-100">
                @svg('heroicon-o-arrow-left', 'h-4 w-4 mr-2 text-gray-600')
                Kembali
            </a>

            {{-- Judul --}}
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Pemeriksaan') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('pemeriksaan.update', $pemeriksaan) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-3 md:grid-cols-3 gap-4">
                    <x-input-group type="text" label="Nama Pasien" name="nama_pasien" value="{{ old('nama_pasien', $pemeriksaan->nama_pasien) }}" />
                    <x-input-group type="datetime-local" label="Waktu Pemeriksaan" name="waktu_pemeriksaan" value="{{ old('waktu_pemeriksaan', \Carbon\Carbon::parse($pemeriksaan->waktu_pemeriksaan)->format('Y-m-d\TH:i')) }}" />
                </div>

                <div class="grid grid-cols-4 md:grid-cols-4 gap-4 mt-4">
                    @foreach (['tinggi_badan','berat_badan','systole','diastole','heart_rate','respiration_rate','suhu_tubuh'] as $field)
                        <x-input-group type="number" label="{{ ucwords(str_replace('_', ' ', $field)) }}" name="{{ $field }}" :value="$pemeriksaan->$field" />
                    @endforeach
                </div>

                <div class="grid grid-cols-2 md:grid-cols-2 gap-4 mt-4">
                    <div class="w-full">
                        <x-input-label for="catatan" value="Catatan Dokter" />
                        <textarea name="catatan" id="catatan" rows="4" class="w-full border rounded">{{ old('catatan', $pemeriksaan->catatan) }}</textarea>
                        <x-input-error :messages="$errors->get('catatan')" />
                    </div>
                </div>

                <div class="mt-8">
                    <h3 class="text-xl font-semibold mb-4 text-gray-700">Resep Obat</h3>

                    <div id="resep-container" class="space-y-4">
                        @foreach ($pemeriksaan->reseps as $i => $resep)
                            <div class="resep-item bg-gray-50 p-4 rounded-md shadow-sm border grid grid-cols-12 gap-4 relative">
                                <div class="col-span-4">
                                    <select name="resep[{{ $i }}][medicine_id]" data-index="{{ $i }}" class="medicine-select w-full border rounded-md p-2" required data-initial-id="{{ $resep->medicine_id }}" data-initial-name="{{ $resep->medicine_name }}"></select>
                                </div>
                                <div class="col-span-3">
                                    <x-text-input name="resep[{{ $i }}][dosage]" placeholder="Dosis (misal: 3x1)" class="border p-2 w-full rounded-md" value="{{ $resep->dosage }}" />
                                </div>
                                <div class="col-span-3">
                                    <x-text-input type="number" name="resep[{{ $i }}][quantity]" placeholder="Jumlah" class="border p-2 w-full rounded-md" value="{{ $resep->quantity }}" />
                                </div>

                                <x-text-input type="hidden" name="resep[{{ $i }}][medicine_name]" class="medicine-name-hidden" value="{{ $resep->medicine_name }}" />
                                <x-text-input type="hidden" name="resep[{{ $i }}][medicine_price]" class="medicine-price-hidden" value="{{ $resep->medicine_price }}" />
                                
                                <button type="button"
                                    class="btn-delete-resep absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white text-xs px-2 py-1 rounded-full shadow">
                                    ✕
                                </button>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        <x-secondary-button id="add-resep">+ Tambah Resep</x-secondary-button>
                    </div>
                </div>

                <div class="mt-6">
                    <h3 class="text-lg font-semibold mt-6 mb-2">Berkas Pemeriksaan</h3>
                    <div class="mb-4">
                        <p>Berkas saat ini:</p>
                        @forelse ($pemeriksaan->berkas as $file)
                            <div class="flex items-center gap-3 mb-1">
                                <a href="{{ Storage::url($file->file_path) }}" target="_blank" class="text-blue-600 underline">
                                    Lihat File {{ $loop->iteration }}
                                </a>
                                <label class="inline-flex items-center text-sm text-red-600">
                                    <input type="checkbox" name="hapus_berkas[]" value="{{ $file->id }}" class="mr-1">
                                    Hapus
                                </label>
                            </div>
                        @empty
                            <p class="text-gray-500 italic">Tidak ada berkas.</p>
                        @endforelse
                    </div>
                    <div class="mb-4">
                        <x-input-label for="berkas[]" value="Upload Berkas Baru (boleh lebih dari satu)" />
                        <input type="file" name="berkas[]" multiple class="border w-full rounded-md" />
                        <x-input-error :messages="$errors->get('berkas')" />
                    </div>
                </div>

                <x-primary-button>Simpan Perubahan</x-primary-button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let resepIndex = {{ $pemeriksaan->reseps->count() }};

            function initSelect2(select) {
                $(select).select2({
                    allowClear: true,
                    placeholder: "Cari nama obat...",
                    ajax: {
                        url: '{{ route('ajax.obat.autocomplete') }}',
                        dataType: 'json',
                        delay: 250,
                        data: params => ({ q: params.term }),
                        processResults: function (data) {
                            return {
                                results: data.map(item => ({
                                    id: item.id,
                                    text: item.name
                                }))
                            };
                        }
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

            $('.medicine-select').each(function () {
                initSelect2(this);
            });

            $('#add-resep').on('click', function () {
                let container = document.getElementById('resep-container');
                let index = container.querySelectorAll('.resep-item').length;

                const newRow = `
                    <div class="resep-item bg-gray-50 p-4 rounded-md shadow-sm border grid grid-cols-12 gap-4 relative">
                        <div class="col-span-4">
                            <select name="resep[${resepIndex}][medicine_id]" class="medicine-select w-full border rounded-md p-2" data-index="${resepIndex}" placeholder="Pilih obat..."></select>
                        </div>
                        <div class="col-span-3">
                            <x-text-input name="resep[${resepIndex}][dosage]" placeholder="Dosis (misal: 3x1)" class="w-full border p-2 rounded-md" />
                        </div>
                        <div class="col-span-3">
                            <x-text-input type="number" name="resep[${resepIndex}][quantity]" placeholder="Jumlah" class="w-full border p-2 rounded-md" />
                        </div>

                        <x-text-input type="hidden" name="resep[${resepIndex}][medicine_name]" class="medicine-name-hidden" />
                        <x-text-input type="hidden" name="resep[${resepIndex}][medicine_price]" class="medicine-price-hidden" />
                        
                        <button type="button"
                            class="btn-delete-resep absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white text-xs px-2 py-1 rounded-full shadow">
                            ✕
                        </button>
                    </div>
                `;

                $('#resep-container').append(newRow);
                initSelect2(`#resep-container .resep-item:last-child .medicine-select`);
                resepIndex++;
            });

            $('#resep-container').on('click', '.btn-delete-resep', function () {
                $(this).closest('.resep-item').remove();
            });
        });
    </script>
</x-app-layout>
