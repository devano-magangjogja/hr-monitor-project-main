<?php

namespace App\Http\Controllers;

use App\Models\Pemagang;
use Illuminate\Http\Request;

class PemagangController extends Controller
{
    /**
     * Simpan data pemagang baru (dapat diakses oleh Admin & Staff)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'no_hp' => ['required', 'string', 'max:13', 'unique:pemagang,no_hp'],
            'kampus' => ['required', 'string', 'max:255'],
            'divisi' => ['required', 'string', 'max:100'],
        ], [
            'nama_lengkap.required' => 'Nama lengkap pemagang wajib diisi.',
            'no_hp.required' => 'Nomor WhatsApp / HP wajib diisi.',
            'no_hp.unique' => 'Nomor WhatsApp / HP sudah terdaftar untuk pemagang lain.',
            'kampus.required' => 'Asal kampus / sekolah wajib diisi.',
            'divisi.required' => 'Divisi magang wajib dipilih atau diisi.',
        ]);

        Pemagang::create($validated);

        return redirect()->back()->with('success', "Pemagang {$validated['nama_lengkap']} berhasil ditambahkan.");
    }
}
