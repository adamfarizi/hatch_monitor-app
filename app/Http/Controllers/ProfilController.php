<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfilController extends Controller
{
    public function index($id_peternak)
    {
        $data['title'] = 'Profil';
        
        $peternak = User::where('id_peternak', $id_peternak)->first();

        return view('auth.profil.profil',[
            'peternak' => $peternak,
        ],$data);
    }

    public function edit(Request $request, $id_peternak)
    {
        try {
            // Validasi input
            $this->validate($request, [
                'nama' => 'required', 
                'email' => 'required',
            ], [
                'nama.required' => 'Masukkan terlebih dahulu!',
                'email.required' => 'Masukkan terlebih dahulu!',
            ]);
    
            // Buat data pembayaran
            User::where('id_peternak', $id_peternak)->update([
                'nama' => $request->input('nama'),
                'email' => $request->input('email'),
                'updated_at' => now(),
            ]);
    
            return redirect()->route('profil', ['id_peternak' => $id_peternak])->with('success', 'Data berhasil diubah.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function changePassword(Request $request)
    {
        try {
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:8|different:current_password',
                'new_password_confirmation' => 'required|same:new_password',
            ]);

            $user = Auth::user();

            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()->withErrors(['current_password' => 'Konfirmasi password salah !']);
            }
            
            User::where('id_peternak', $user->id_peternak)->update([
                'password' => Hash::make($request->new_password),
                'updated_at' => now(),
            ]);

            return redirect()->route('profil', ['id_peternak' => $user->id_peternak])->with('success', 'Password berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error: ' . $e->getMessage()]);
        }
    }
}
