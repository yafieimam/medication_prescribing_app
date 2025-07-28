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
                {{ __('Input Pemeriksaan') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 text-red-600 rounded">
                    <ul class="list-disc pl-5 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('pemeriksaan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-3 md:grid-cols-3 gap-4">
                    <x-input-group type="text" label="Nama Pasien" name="nama_pasien" :value="old('nama_pasien')" />
                    <x-input-group type="date" label="Waktu Pemeriksaan" name="waktu_pemeriksaan" :value="old('waktu_pemeriksaan')" />
                </div>

                <div class="grid grid-cols-4 md:grid-cols-4 gap-4 mt-4">
                    <x-input-group type="number" label="Tinggi Badan (cm)" name="tinggi_badan" :value="old('tinggi_badan')" />
                    <x-input-group type="number" label="Berat Badan (kg)" name="berat_badan" :value="old('berat_badan')" />
                    <x-input-group type="number" label="Systole" name="systole" :value="old('systole')" />
                    <x-input-group type="number" label="Diastole" name="diastole" :value="old('diastole')" />
                </div>

                <div class="grid grid-cols-4 md:grid-cols-4 gap-4 mt-4">
                    <x-input-group type="number" label="Heart Rate" name="heart_rate" :value="old('heart_rate')" />
                    <x-input-group type="number" label="Respiration Rate" name="respiration_rate" :value="old('respiration_rate')" />
                    <x-input-group type="number" label="Suhu Tubuh (°C)" name="suhu_tubuh" :value="old('suhu_tubuh')" />
                </div>

                <div class="grid grid-cols-2 md:grid-cols-2 gap-4 mt-4">
                    <div class="w-full">
                        <x-input-label for="catatan" value="Catatan Dokter" />
                        <textarea name="catatan" id="catatan" class="w-full border rounded-md" rows="4">{{ old('catatan') }}</textarea>
                        <x-input-error :messages="$errors->get('catatan')" />
                    </div>
                </div>

                <div class="mt-6">
                    <x-input-label for="berkas[]" value="Upload Berkas Pemeriksaan (boleh lebih dari satu)" />
                    <input type="file" name="berkas[]" multiple class="w-full border rounded-md" />
                    <x-input-error :messages="$errors->get('berkas')" />
                </div>

                <div class="mt-6">
                    <h3 class="text-lg font-semibold mb-2">Resep Obat</h3>

                    <div id="resep-container">
                        <div class="resep-item grid grid-cols-4 gap-4 mb-2">
                            <select name="resep[0][medicine_id]" class="medicine-select w-full border p-1" data-index="0"></select>
                            <x-text-input name="resep[0][dosage]" placeholder="Dosis (misal: 3x1)" class="border p-1 w-full" />
                            <x-text-input type="number" name="resep[0][quantity]" placeholder="Jumlah" class="border p-1 w-full" />
                            <x-text-input type="hidden" name="resep[0][medicine_name]" class="medicine-name-hidden" />
                            <x-text-input type="hidden" name="resep[0][medicine_price]" class="medicine-price-hidden" />
                            <button type="button" class="btn-delete-resep text-red-500">Hapus</button>
                        </div>
                    </div>

                    <x-secondary-button id="add-resep">+ Tambah Obat</x-secondary-button>
                </div>

                <x-primary-button class="mt-4">Simpan Pemeriksaan</x-primary-button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            function bindAutocomplete(index) {
                const select = document.querySelector(`select.medicine-select[data-index="${index}"]`);

                $(select).select2({
                    placeholder: "Cari nama obat...",
                    ajax: {
                        url: "{{ route('ajax.obat.autocomplete') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return { q: params.term };
                        },
                        processResults: function (data) {
                            console.log(data);
                            return {
                                results: data.map(item => ({
                                    id: item.id,
                                    text: item.name
                                }))
                            };
                        }
                    }
                });

                $(select).on('select2:select', function (e) {
                    const data = e.params.data;
                    const index = select.dataset.index;

                    // Ambil harga dari API
                    fetch(`/ajax/harga-obat?medicine_id=${data.id}&tanggal={{ request()->old('waktu_pemeriksaan') ?? now()->toDateString() }}`)
                        .then(res => res.json())
                        .then(res => {
                            document.querySelector(`input[name="resep[${index}][medicine_name]"]`).value = data.text;
                            document.querySelector(`input[name="resep[${index}][medicine_price]"]`).value = res.harga ?? 0;
                        });
                });
            }

            bindAutocomplete(0);

            document.getElementById('add-resep').addEventListener('click', function () {
                let container = document.getElementById('resep-container');
                let index = container.querySelectorAll('.resep-item').length;
                let html = `
                    <div class="resep-item grid grid-cols-4 gap-4 mb-2">
                        <select name="resep[${index}][medicine_id]" class="medicine-select w-full border p-1" data-index="${index}"></select>
                        <x-text-input name="resep[${index}][dosage]" placeholder="Dosis" class="border p-1 w-full" />
                        <x-text-input type="number" name="resep[${index}][quantity]" placeholder="Jumlah" class="border p-1 w-full" />
                        <x-text-input type="hidden" name="resep[${index}][medicine_name]" />
                        <x-text-input type="hidden" name="resep[${index}][medicine_price]" />
                        <button type="button" class="btn-delete-resep text-red-500">Hapus</button>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', html);
                bindAutocomplete(index);
            });

            $('#resep-container').on('click', '.btn-delete-resep', function () {
                $(this).closest('.resep-item').remove();
            });
        });
    </script>
</x-app-layout>
