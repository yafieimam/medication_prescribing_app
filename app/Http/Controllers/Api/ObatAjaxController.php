<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ObatApiService;

class ObatAjaxController extends Controller
{
    public function autocomplete(Request $request, ObatApiService $obatApi)
    {
        $search = $request->query('q', '');
        $data = $obatApi->searchMedicines($search);

        // Format untuk select2
        // $results = collect($data)->map(function ($item) {
        //     return [
        //         'id' => $item['id'],
        //         'name' => $item['name'],
        //     ];
        // });

        return response()->json($data);
    }

    public function harga(Request $request, ObatApiService $obatApi)
    {
        $medicineId = $request->query('medicine_id');
        $tanggal = $request->query('tanggal') ?? now()->toDateString();

        $harga = $obatApi->getMedicinePrice($medicineId, $tanggal);

        return response()->json(['harga' => $harga]);
    }
}
