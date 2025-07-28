<?php

namespace App\Http\Controllers;

use App\Models\Pemeriksaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class PembayaranController extends Controller
{
    public function index()
    {
        $pemeriksaan = Pemeriksaan::with('reseps')
            ->latest()
            ->get();

        return view('pembayaran.index', compact('pemeriksaan'));
    }

    public function show(Pemeriksaan $pemeriksaan)
    {
        return view('pembayaran.show', compact('pemeriksaan'));
    }

    public function selesaikan(Pemeriksaan $pemeriksaan)
    {
        $pemeriksaan->update(['sudah_dilayani' => 1]);

        Log::info('Apoteker menyelesaikan pembayaran pemeriksaan', [
            'user_id' => auth()->id(),
            'pemeriksaan_id' => $pemeriksaan->id,
        ]);

        return redirect()->route('pembayaran.index')->with('success', 'Pembayaran berhasil diselesaikan.');
    }

    public function cetak(Pemeriksaan $pemeriksaan)
    {
        $pdf = Pdf::loadView('pembayaran.pdf', compact('pemeriksaan'));

        return $pdf->download("resi_pembayaran_{$pemeriksaan->id}.pdf");
    }
}
