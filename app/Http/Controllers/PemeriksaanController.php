<?php

namespace App\Http\Controllers;

use App\Models\Pemeriksaan;
use App\Models\PemeriksaanBerkas;
use App\Models\Resep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\ObatApiService;

class PemeriksaanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pemeriksaan = Pemeriksaan::withCount(['reseps', 'berkas'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('pemeriksaan.index', compact('pemeriksaan'));
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pemeriksaan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pasien' => 'required|string',
            'waktu_pemeriksaan' => 'required|date',
            'tinggi_badan' => 'required|numeric',
            'berat_badan' => 'required|numeric',
            'systole' => 'required|integer',
            'diastole' => 'required|integer',
            'heart_rate' => 'required|integer',
            'respiration_rate' => 'required|integer',
            'suhu_tubuh' => 'required|numeric',
            'catatan' => 'nullable|string',
            'berkas.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'resep' => 'nullable|array',
            'resep.*.medicine_id' => 'nullable|string',
            'resep.*.dosage' => 'nullable|string',
            'resep.*.quantity' => 'nullable|integer|min:1',
            'resep.*.medicine_price' => 'nullable|integer|min:1',
        ]);

        $validated['dokter_id'] = auth()->id();

        try {
            $pemeriksaan = Pemeriksaan::create($validated);

            if ($request->hasFile('berkas')) {
                foreach ($request->file('berkas') as $file) {
                    $path = $file->store('pemeriksaan/berkas', 'public');
                    PemeriksaanBerkas::create([
                        'pemeriksaan_id' => $pemeriksaan->id,
                        'file_path' => $path,
                    ]);
                }
            }

            foreach ($request->input('resep', []) as $item) {
                Resep::create([
                    'pemeriksaan_id' => $pemeriksaan->id,
                    'medicine_id' => $item['medicine_id'],
                    'medicine_name' => $item['medicine_name'],
                    'dosage' => $item['dosage'],
                    'quantity' => $item['quantity'],
                    'prices' => $item['medicine_price'],
                ]);
            }

            Log::info('Creating Data Pemeriksaan and Resep', [
                'user_id' => auth()->id(),
                'pemeriksaan_id' => $pemeriksaan->id,
                'jumlah_resep' => count($request->input('resep', [])),
                'jumlah_berkas' => count($request->file('berkas', [])),
            ]);

            return redirect()->route('pemeriksaan.index')->with('success', 'Pemeriksaan berhasil disimpan.');
        } catch (\Throwable $e) {
            Log::error('Failed to save data pemeriksaan and resep', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors('Something went wrong in save data pemeriksaan and resep.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Pemeriksaan $pemeriksaan)
    {
        $pemeriksaan->load(['reseps', 'berkas']);

        return view('pemeriksaan.show', compact('pemeriksaan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pemeriksaan $pemeriksaan)
    {
        if ($pemeriksaan->sudah_dilayani === 1) {
            abort(403, 'Pemeriksaan sudah tidak bisa diedit.');
        }

        $pemeriksaan->load(['resep', 'files']);

        return view('pemeriksaan.edit', compact('pemeriksaan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pemeriksaan $pemeriksaan)
    {
        if ($pemeriksaan->sudah_dilayani === 0) {
            abort(403, 'Pemeriksaan sudah tidak bisa diedit.');
        }

        $validated = $request->validate([
            'nama_pasien' => 'required|string|max:255',
            'waktu_pemeriksaan' => 'required|date',
            'tinggi_badan' => 'nullable|numeric',
            'berat_badan' => 'nullable|numeric',
            'systole' => 'nullable|numeric',
            'diastole' => 'nullable|numeric',
            'heart_rate' => 'nullable|numeric',
            'respiration_rate' => 'nullable|numeric',
            'suhu_tubuh' => 'nullable|numeric',
            'catatan' => 'nullable|string',
            'resep' => 'nullable|array',
            'resep.*.medicine_id' => 'required|string',
            'resep.*.jumlah' => 'required|numeric|min:1',
            'files.*' => 'nullable|file|max:2048',
        ]);

        // Update pemeriksaan
        $pemeriksaan->update([
            'nama_pasien' => $request->nama_pasien,
            'waktu_pemeriksaan' => $request->waktu_pemeriksaan,
            'tinggi_badan' => $request->tinggi_badan,
            'berat_badan' => $request->berat_badan,
            'systole' => $request->systole,
            'diastole' => $request->diastole,
            'heart_rate' => $request->heart_rate,
            'respiration_rate' => $request->respiration_rate,
            'suhu_tubuh' => $request->suhu_tubuh,
            'catatan' => $request->catatan,
        ]);

        // Hapus resep lama
        $pemeriksaan->resep()->delete();

        // Simpan resep baru
        $obatApi = app(ObatApiService::class);
        foreach ($request->resep ?? [] as $resep) {
            $harga = $obatApi->getMedicinePrice($resep['medicine_id'], $request->waktu_pemeriksaan);

            $pemeriksaan->resep()->create([
                'medicine_id' => $resep['medicine_id'],
                'nama_obat' => $obatApi->getNamaObat($resep['medicine_id']),
                'jumlah' => $resep['jumlah'],
                'harga_satuan' => $harga ?? 0,
            ]);
        }

        // Simpan file baru jika ada
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('berkas', 'public');

                $pemeriksaan->files()->create([
                    'path' => $path,
                ]);
            }
        }

        // Logging
        Log::info('Pemeriksaan diperbarui oleh dokter.', [
            'dokter_id' => auth()->id(),
            'pemeriksaan_id' => $pemeriksaan->id,
        ]);

        return redirect()->route('pemeriksaan.show', $pemeriksaan)
            ->with('success', 'Pemeriksaan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pemeriksaan $pemeriksaan)
    {
        //
    }
}
