<?php

namespace App\Http\Controllers;

use App\Models\Pemeriksaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PemeriksaanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pemeriksaan.create');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
        ]);

        $validated['dokter_id'] = auth()->id();

        $pemeriksaan = Pemeriksaan::create($validated);

        Log::info('Creating Data Pemeriksaan ID: ' . $pemeriksaan->id());

        return redirect()->route('pemeriksaan.index')->with('success', 'Pemeriksaan berhasil disimpan.');
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
