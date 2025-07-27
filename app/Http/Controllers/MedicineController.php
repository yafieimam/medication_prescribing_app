<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ObatApiService;

class MedicineController extends Controller
{
    public function index(ObatApiService $api)
    {
        try {
            $medicines = $api->getMedicines();
            return view('medicines.index', compact('medicines'));
        } catch (\Exception $e) {
            return back()->withErrors('Gagal mengambil data obat.');
        }
    }
}
