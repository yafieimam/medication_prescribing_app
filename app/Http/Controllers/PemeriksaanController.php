<?php

namespace App\Http\Controllers;

use App\Models\Pemeriksaan;
use App\Models\PemeriksaanBerkas;
use App\Models\Resep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pemeriksaan $pemeriksaan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pemeriksaan $pemeriksaan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pemeriksaan $pemeriksaan)
    {
        //
    }
}
