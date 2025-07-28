<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Input Pemeriksaan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('pemeriksaan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div>
                    <x-input-label for="nama_pasien" value="Nama Pasien" />
                    <x-text-input name="nama_pasien" id="nama_pasien" class="block w-full" required />
                    <x-input-error :messages="$errors->get('nama_pasien')" />
                </div>

                <div class="mt-4">
                    <x-input-label for="waktu_pemeriksaan" value="Waktu Pemeriksaan" />
                    <x-text-input name="waktu_pemeriksaan" id="waktu_pemeriksaan" type="date" class="block w-full" required />
                    <x-input-error :messages="$errors->get('waktu_pemeriksaan')" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <x-input-group label="Tinggi Badan (cm)" name="tinggi_badan" />
                    <x-input-group label="Berat Badan (kg)" name="berat_badan" />
                    <x-input-group label="Systole" name="systole" />
                    <x-input-group label="Diastole" name="diastole" />
                    <x-input-group label="Heart Rate" name="heart_rate" />
                    <x-input-group label="Respiration Rate" name="respiration_rate" />
                    <x-input-group label="Suhu Tubuh (°C)" name="suhu_tubuh" />
                </div>

                <div class="mt-4">
                    <x-input-label for="catatan" value="Catatan Dokter" />
                    <textarea name="catatan" id="catatan" class="w-full border rounded-md" rows="4"></textarea>
                    <x-input-error :messages="$errors->get('catatan')" />
                </div>

                <div class="mt-6">
                    <x-input-label for="berkas[]" value="Upload Berkas Pemeriksaan (boleh lebih dari satu)" />
                    <input type="file" name="berkas[]" multiple class="w-full border rounded-md" />
                    <x-input-error :messages="$errors->get('berkas')" />
                </div>

                <div class="mt-6">
                    <h3 class="text-lg font-semibold mb-2">Resep Obat</h3>

                    <div id="resep-container">
                        <div class="resep-item grid grid-cols-3 gap-4 mb-2">
                            <input type="text" name="resep[0][medicine_name]" placeholder="Nama Obat" class="border p-1 w-full" />
                            <input type="text" name="resep[0][dosage]" placeholder="Dosis (misal: 3x1)" class="border p-1 w-full" />
                            <input type="number" name="resep[0][quantity]" placeholder="Jumlah" class="border p-1 w-full" />
                        </div>
                    </div>

                    <x-secondary-button id="add-resep">+ Tambah Obat</x-secondary-button>
                </div>

                <x-primary-button class="mt-4">Simpan Pemeriksaan</x-primary-button>
            </form>
        </div>
    </div>
</x-app-layout>
