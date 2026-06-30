<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class SupplierController extends Controller
{
    public function index()
    {
        $response = Http::withoutVerifying()
            ->get('https://be-ams.sanohindonesia.co.id/api/public/supplier-data/suppliers');

        if (!$response->successful()) {

            return response()->json([
                'success' => false,
                'message' => 'API failed',
                'data' => []
            ], 500);
        }

        $data = $response->json();

        // AMAN: cek apakah ada key data
        return response()->json(
            $data['data'] ?? $data
        );
    }
}