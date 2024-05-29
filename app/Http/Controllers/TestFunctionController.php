<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TestFunctionController extends Controller
{
    public function detectObjects(Request $request)
    {
        // Mendapatkan gambar dari permintaan yang dikirim oleh frontend
        $image = $request->file('image');

        // Mengirim gambar ke endpoint Flask
        $response = Http::attach(
            'image', 
            file_get_contents($image->getRealPath()), 
            $image->getClientOriginalName()
        )->post('http://localhost:8500/detect-objects');

        // Mengembalikan hasil deteksi objek kepada frontend
        return $response->json();
    }

}
